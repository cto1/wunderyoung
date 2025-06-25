<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php'; 
use Mailgun\Mailgun;

require_once 'conf.php';
require_once 'EmailTemplates.php';


class UserAuthAPI {
    private $db;
    private $pdo;
    private $mailgun;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
        $this->mailgun = Mailgun::create(getenv('MAILGUN_API_KEY'));
    }

    private function sendEmail($to, $subject, $textContent, $htmlContent, $bcc = null) {
        $domain = getenv('MAILGUN_DOMAIN');
        if (!$domain) {
            error_log('MAILGUN_DOMAIN not configured');
            return false;
        }

        try {
            $mailgunData = [
                'from'    => 'ExactSum <notifications@' . $domain . '>',
                'to'      => $to,
                'subject' => $subject,
                'text'    => $textContent,
                'html'    => $htmlContent
            ];
            
            // Add BCC if provided
            if ($bcc) {
                $mailgunData['bcc'] = $bcc;
            }
            
            $this->mailgun->messages()->send($domain, $mailgunData);
            return true;
        } catch (\Exception $e) {
            error_log('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    private function sendLoginEmail($email, $token) {
        // Generate direct login link without double encoding
        $loginLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($email);
        error_log('Login link: ' . $loginLink);
        $emailContent = EmailTemplates::prepareLoginEmail($email, $loginLink);
        return $this->sendEmail($email, $emailContent['subject'], $emailContent['textContent'], $emailContent['htmlContent']);
    }

    private function sendInviteEmail($email, $orgName, $inviterEmail, $token) {
        // Generate direct invite link without double encoding
        $inviteLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($email);
        $emailContent = EmailTemplates::prepareUserInvitationEmail($orgName, $inviterEmail, $inviteLink);
        // BCC aleks@titanfintech.com on all invitation emails
        return $this->sendEmail($email, $emailContent['subject'], $emailContent['textContent'], $emailContent['htmlContent'], 'aleks@titanfintech.com');
    }

    // 1. Sign up with email
    public function signup($data) {
        try {
            if (!isset($data['email'])) {
                throw new Exception('Email is required');
            }
    
            $this->pdo->beginTransaction();
                
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                throw new Exception('User already exists');
            }
    
            // Create default organization (will include all default settings)
            $orgApi = new OrgAPI();
            $orgResult = $orgApi->create([
                'name' => 'ExactSum'  // Default values will be used for all other fields
            ]);
    
            if ($orgResult['status'] !== 'success') {
                throw new Exception('Failed to create organization: ' . ($orgResult['message'] ?? 'Unknown error'));
            }
    
            // Create user
            $userId = 'user_' . uniqid();
            $stmt = $this->pdo->prepare("
                INSERT INTO users (
                    user_id, org_id, email, email_verified, role
                ) VALUES (
                    :user_id, :org_id, :email, :email_verified, 'owner'
                )
            ");
            
            $stmt->execute([
                ':user_id' => $userId,
                ':org_id' => $orgResult['org_id'],
                ':email' => $data['email'],
                ':email_verified' => false
            ]);
    
            $this->pdo->commit();
            return ['status' => 'success', 'org_id' => $orgResult['org_id'], 'user_id' => $userId];
    
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 2. Request passwordless login
    public function requestLogin($email) {
        try {
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT user_id, email_verified, org_id, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found');
            }

            // Generate login token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save token
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET login_token = ?, token_expires_at = ?
                WHERE email = ?
            ");
            $stmt->execute([$token, $expires, $email]);

            // Send email with login link
            if (!$this->sendLoginEmail($email, $token)) {
                throw new Exception('Failed to send login email');
            }

            return [
                'status' => 'success',
                'message' => 'Login link sent to email',
                'user_id' => $user['user_id'],
                'org_id' => $user['org_id'],
                'role' => $user['role'],
                'email' => $email,
                'token' => $token
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 3. Verify login token
    public function verifyLogin($email, $token) {
        try {
            // Additional layer of validation inside the function for security
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            if (!preg_match('/^[0-9a-f]{64}$/', $token)) {
                throw new Exception('Invalid token format');
            }
            
            $stmt = $this->pdo->prepare("
                SELECT user_id, org_id, email, role
                FROM users 
                WHERE email = ? 
                AND login_token = ? 
                AND token_expires_at > CURRENT_TIMESTAMP
            ");
            $stmt->execute([$email, $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                throw new Exception('Invalid or expired token');
            }

            // Get user agent for bot detection
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Check if this looks like an email client crawler/bot
            $isEmailCrawler = $this->isEmailClientCrawler($userAgent);
            
            // Log this access attempt
            $this->logLoginAttempt($email, 'magic_link', true, $isEmailCrawler ? 'Email client crawler access' : 'User access');
            
            // If it's an email client crawler, don't invalidate the token
            // Just return success so the email client gets a 200 response
            if ($isEmailCrawler) {
                return [
                    'status' => 'success',
                    'user_id' => $user['user_id'],
                    'org_id' => $user['org_id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'crawler_access' => true // Flag to indicate this was crawler access
                ];
            }
            
            // For real users, just mark email as verified but keep token active
            // Token will only be invalidated after successful JWT generation
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email_verified = 1
                WHERE email = ?
            ");
            $stmt->execute([$email]);
    
            return [
                'status' => 'success',
                'user_id' => $user['user_id'],
                'org_id' => $user['org_id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
    
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // PASSWORD AUTHENTICATION METHODS

    private function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Enhanced password complexity validation
    private function validatePasswordComplexity($password) {
        $errors = [];
        
        // Minimum length
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        // Maximum length (prevent DoS)
        if (strlen($password) > 128) {
            $errors[] = 'Password must not exceed 128 characters';
        }
        
        // Character diversity requirements
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        // Check against common passwords
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password is too common. Please choose a more unique password';
        }
        
        // Check for repeated characters
        if (preg_match('/(.)\1{2,}/', $password)) {
            $errors[] = 'Password cannot contain more than 2 consecutive identical characters';
        }
        
        return $errors;
    }
    
    // Check against common password list
    private function isCommonPassword($password) {
        $commonPasswords = [
            'password', 'password123', '123456', '123456789', 'qwerty', 'abc123',
            'password1', 'admin', 'letmein', 'welcome', 'monkey', '1234567890',
            'password12', 'qwerty123', 'password!', 'Password1', 'Password123',
            'admin123', 'root', 'toor', 'pass', 'test', 'guest', 'user',
            'changeme', 'newpassword', 'secret', 'login', 'passw0rd'
        ];
        
        return in_array(strtolower($password), array_map('strtolower', $commonPasswords));
    }
    
    // Rate limiting and brute force protection
    private function checkRateLimit($email, $action = 'login') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = $action . '_' . $email . '_' . $ip;
        
        // Get recent attempts from database
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as attempts, MAX(created_at) as last_attempt
            FROM login_attempts 
            WHERE email = ? AND ip_address = ? AND action = ? 
            AND created_at > datetime('now', '-15 minutes')
        ");
        $stmt->execute([$email, $ip, $action]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $attempts = $result['attempts'] ?? 0;
        $lastAttempt = $result['last_attempt'] ?? null;
        
        // Rate limiting rules
        $maxAttempts = 5; // 5 attempts per 15 minutes
        $lockoutDuration = 15; // 15 minutes
        
        if ($attempts >= $maxAttempts) {
            $timeSinceLastAttempt = $lastAttempt ? (time() - strtotime($lastAttempt)) : $lockoutDuration * 60;
            $remainingLockout = ($lockoutDuration * 60) - $timeSinceLastAttempt;
            
            if ($remainingLockout > 0) {
                $minutes = ceil($remainingLockout / 60);
                throw new Exception("Too many failed attempts. Please try again in {$minutes} minutes.");
            }
        }
        
        return true;
    }
    
    // Log login attempts
    private function logLoginAttempt($email, $action, $success, $reason = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO login_attempts (
                    email, ip_address, user_agent, action, success, reason, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
            ");
            $stmt->execute([$email, $ip, $userAgent, $action, $success ? 1 : 0, $reason]);
        } catch (Exception $e) {
            error_log('Failed to log login attempt: ' . $e->getMessage());
        }
    }
    
    // Check if account is locked
    private function isAccountLocked($email) {
        // Check for account lockout (too many failed attempts)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as failed_attempts
            FROM login_attempts 
            WHERE email = ? AND success = 0 
            AND created_at > datetime('now', '-1 hour')
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $failedAttempts = $result['failed_attempts'] ?? 0;
        
        // Lock account after 10 failed attempts in 1 hour
        if ($failedAttempts >= 10) {
            throw new Exception('Account temporarily locked due to too many failed login attempts. Please try again later or use magic link login.');
        }
        
        return false;
    }

    // Check if user agent indicates an email client crawler/bot
    private function isEmailClientCrawler($userAgent) {
        if (empty($userAgent) || $userAgent === 'unknown') {
            return false;
        }
        
        $userAgent = strtolower($userAgent);
        
        // Common email client crawlers and link prefetching bots
        $crawlerPatterns = [
            'mimecast',           // Mimecast email security
            'proofpoint',         // Proofpoint email security
            'microsoft.atp',      // Microsoft Advanced Threat Protection
            'atp safelinks',      // Microsoft ATP Safe Links
            'office 365',         // Office 365 link protection
            'outlookbot',         // Outlook link preview
            'linkguard',          // Various link protection services
            'mailguard',          // MailGuard email security
            'barracuda',          // Barracuda email security
            'sophos',             // Sophos email protection
            'symantec',           // Symantec email security
            'trend micro',        // Trend Micro email security
            'cisco email security', // Cisco email security
            'forcepoint',         // Forcepoint email security
            'mcafee',             // McAfee email protection
            'kaspersky',          // Kaspersky email security
            'avira',              // Avira email protection
            'bitdefender',        // Bitdefender email security
            'eset',               // ESET email security
            'f-secure',           // F-Secure email protection
            'google messagescreen', // Google Workspace security
            'amavisd',            // Amavisd-new mail filter
            'clamav',             // ClamAV antivirus
            'spamassassin',       // SpamAssassin
            'postfix',            // Postfix mail server
            'sendmail',           // Sendmail
            'exim',               // Exim mail server
            'postmaster',         // Mail server checks
            'mailscanner',        // MailScanner
            'securitygateway',    // Generic security gateway
            'emailsecurity',      // Generic email security
            'antispam',           // Generic anti-spam
            'antivirus',          // Generic antivirus
            'safelinks',          // Generic safe links
            'linkcheck',          // Generic link checking
            'urlcheck',           // Generic URL checking
            'prefetch',           // Browser/client prefetching
            'preload',            // Browser/client preloading
        ];
        
        foreach ($crawlerPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        // Also check for very short user agents (often bots)
        if (strlen($userAgent) < 10) {
            return true;
        }
        
        // Check for missing common browser indicators
        $browserIndicators = ['mozilla', 'webkit', 'chrome', 'firefox', 'safari', 'edge', 'opera'];
        $hasBrowserIndicator = false;
        
        foreach ($browserIndicators as $indicator) {
            if (strpos($userAgent, $indicator) !== false) {
                $hasBrowserIndicator = true;
                break;
            }
        }
        
        // If no browser indicators and user agent is very generic, likely a bot
        if (!$hasBrowserIndicator && (strlen($userAgent) < 20 || 
            strpos($userAgent, 'bot') !== false || 
            strpos($userAgent, 'crawler') !== false ||
            strpos($userAgent, 'spider') !== false)) {
            return true;
        }
        
        return false;
    }

    // Get recent token attempts for analysis
    private function getRecentTokenAttempts($email, $token, $minutesBack = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ip_address, user_agent, action, success, reason, created_at
                FROM login_attempts 
                WHERE email = ? 
                AND action = 'magic_link'
                AND created_at > datetime('now', '-{$minutesBack} minutes')
                ORDER BY created_at DESC
            ");
            $stmt->execute([$email]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting recent token attempts: ' . $e->getMessage());
            return [];
        }
    }

    // 4. Password-based login
    public function passwordLogin($email, $password) {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            if (empty($password)) {
                throw new Exception('Password is required');
            }

            // Check rate limiting and account lockout
            $this->checkRateLimit($email, 'password_login');
            $this->isAccountLocked($email);

            // Get user with password hash
            $stmt = $this->pdo->prepare("
                SELECT user_id, org_id, email, role, password_hash 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->logLoginAttempt($email, 'password_login', false, 'User not found');
                throw new Exception('Invalid credentials');
            }

            // Check if user has password set
            if (empty($user['password_hash'])) {
                $this->logLoginAttempt($email, 'password_login', false, 'No password set');
                throw new Exception('Password not set. Please use magic link login.');
            }

            // Verify password
            if (!$this->verifyPassword($password, $user['password_hash'])) {
                $this->logLoginAttempt($email, 'password_login', false, 'Invalid password');
                throw new Exception('Invalid credentials');
            }

            // Log successful login
            $this->logLoginAttempt($email, 'password_login', true, 'Success');

            // Mark email as verified and return same format as magic link
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email_verified = 1
                WHERE email = ?
            ");
            $stmt->execute([$email]);

            return [
                'status' => 'success',
                'user_id' => $user['user_id'],
                'org_id' => $user['org_id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 5. Check if user has password set
    public function hasPassword($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            return [
                'status' => 'success',
                'has_password' => !empty($user['password_hash'])
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 6. Set password for magic link users
    public function setPassword($userId, $password) {
        try {
            // Validate password complexity
            $complexityErrors = $this->validatePasswordComplexity($password);
            if (!empty($complexityErrors)) {
                throw new Exception('Password requirements not met: ' . implode('. ', $complexityErrors));
            }

            // Check if user already has password
            $stmt = $this->pdo->prepare("SELECT password_hash, email FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('User not found');
            }

            if (!empty($user['password_hash'])) {
                throw new Exception('Password already set. Use change password instead.');
            }

            // Check rate limiting for password setting
            $this->checkRateLimit($user['email'], 'set_password');

            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$this->hashPassword($password), $userId]);

            // Log password set action
            $this->logLoginAttempt($user['email'], 'set_password', true, 'Password set successfully');

            return ['status' => 'success', 'message' => 'Password set successfully. You can now use both login methods.'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 7. Change existing password
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Validate new password complexity
            $complexityErrors = $this->validatePasswordComplexity($newPassword);
            if (!empty($complexityErrors)) {
                throw new Exception('New password requirements not met: ' . implode('. ', $complexityErrors));
            }

            // Get current password hash
            $stmt = $this->pdo->prepare("SELECT password_hash, email FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('User not found');
            }

            if (empty($user['password_hash'])) {
                throw new Exception('No password set. Use set password instead.');
            }

            // Check rate limiting for password changes
            $this->checkRateLimit($user['email'], 'change_password');

            // Verify current password
            if (!$this->verifyPassword($currentPassword, $user['password_hash'])) {
                $this->logLoginAttempt($user['email'], 'change_password', false, 'Invalid current password');
                throw new Exception('Current password is incorrect');
            }

            // Check if new password is the same as current
            if ($this->verifyPassword($newPassword, $user['password_hash'])) {
                throw new Exception('New password must be different from current password');
            }

            // Update to new password
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$this->hashPassword($newPassword), $userId]);

            // Log successful password change
            $this->logLoginAttempt($user['email'], 'change_password', true, 'Password changed successfully');

            return ['status' => 'success', 'message' => 'Password changed successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 8. Remove password (revert to magic link only)
    public function removePassword($userId, $currentPassword) {
        try {
            // Get current password hash
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || empty($user['password_hash'])) {
                throw new Exception('No password to remove');
            }

            // Verify current password
            if (!$this->verifyPassword($currentPassword, $user['password_hash'])) {
                throw new Exception('Current password is incorrect');
            }

            // Remove password
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = NULL, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);

            return ['status' => 'success', 'message' => 'Password removed. You can now only use magic link login.'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 4. Update user profile
    public function updateProfile($userId, $data) {
        try {
            $updates = [];
            $params = [':user_id' => $userId];

            if (isset($data['name'])) {
                $updates[] = "name = :name";
                $params[':name'] = $data['name'];
            }

            if (isset($data['email'])) {
                $updates[] = "email = :email";
                $params[':email'] = $data['email'];
                // Reset verification if email changed
                $updates[] = "email_verified = 0";
            }

            if (empty($updates)) {
                throw new Exception('No fields to update');
            }

            $updates[] = "updated_at = CURRENT_TIMESTAMP";
            
            $sql = "UPDATE users SET " . implode(", ", $updates) . 
                   " WHERE user_id = :user_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ['status' => 'success'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 5. Invite user
    public function inviteUser($orgId, $data, $inviterId) {
        try {
            // Validate input parameters
            if (empty($orgId) || empty($inviterId)) {
                throw new Exception('Missing required parameters: orgId or inviterId');
            }
            
            // Debug logging
            error_log("DEBUG: inviteUser called with orgId: $orgId, inviterId: $inviterId");
            
            // Validate required fields
            if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Valid email address is required');
            }
            
            // Validate role
            $allowedRoles = ['user', 'admin', 'owner'];
            $role = $data['role'] ?? 'user';
            if (!in_array($role, $allowedRoles)) {
                throw new Exception('Invalid role specified. Allowed roles: ' . implode(', ', $allowedRoles));
            }
            
            // Check if inviter has permission
            $stmt = $this->pdo->prepare("
                SELECT role 
                FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$inviterId, $orgId]);
            $inviter = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug logging
            error_log("DEBUG: Query executed for inviter. InviterId: $inviterId, OrgId: $orgId");
            error_log("DEBUG: Inviter query result: " . json_encode($inviter));

            // More detailed error checking
            if (!$inviter) {
                // Additional debug: Check if user exists at all
                $stmt = $this->pdo->prepare("SELECT user_id, org_id, role FROM users WHERE user_id = ?");
                $stmt->execute([$inviterId]);
                $userExists = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("DEBUG: User exists check: " . json_encode($userExists));
                
                throw new Exception('Inviter not found in organization');
            }
            
            if (!isset($inviter['role'])) {
                throw new Exception('Inviter role not found');
            }
            
            if (!in_array($inviter['role'], ['owner', 'admin'])) {
                throw new Exception('Insufficient permissions. Current role: ' . $inviter['role']);
            }
            
            // Only owners can create other owners
            if ($role === 'owner' && $inviter['role'] !== 'owner') {
                throw new Exception('Only organization owners can invite new owners');
            }

            // Check if user with this email already exists in the organization
            $stmt = $this->pdo->prepare("
                SELECT user_id FROM users WHERE email = ? AND org_id = ?
            ");
            $stmt->execute([$data['email'], $orgId]);
            if ($stmt->fetch()) {
                throw new Exception('A user with this email already exists in your organization');
            }

            // Create user
            $userId = 'user_' . uniqid();
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

            // Generate invite link
            $inviteLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($data['email']);

            $stmt = $this->pdo->prepare("
                INSERT INTO users (
                    user_id, org_id, email, role, login_token, token_expires_at
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId, 
                $orgId,
                $data['email'],
                $role,
                $token,
                $expires
            ]);

            // Here you would send invitation email
            $stmt = $this->pdo->prepare("SELECT o.name as org_name, u.email as inviter_email 
                                        FROM organizations o 
                                        JOIN users u ON u.org_id = o.org_id 
                                        WHERE u.user_id = ? AND o.org_id = ?");
            $stmt->execute([$inviterId, $orgId]);
            $orgInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Provide fallback values if query fails
            $orgName = $orgInfo ? ($orgInfo['org_name'] ?? 'ExactSum') : 'ExactSum';
            $inviterEmail = $orgInfo ? ($orgInfo['inviter_email'] ?? 'team@exactsum.com') : 'team@exactsum.com';
            
            if (!$this->sendInviteEmail($data['email'], $orgName, $inviterEmail, $token)) {
                throw new Exception('Failed to send invitation email');
            }

            return [
                'status' => 'success', 
                'user_id' => $userId,
                'invite_link' => $inviteLink,
                'token' => $token,
                'expires_at' => $expires
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Delete user
    public function deleteUser($orgId, $userIdToDelete, $deleterId) {
        try {
            // Check if deleter has permission
            $stmt = $this->pdo->prepare("
                SELECT role 
                FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$deleterId, $orgId]);
            $deleter = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$deleter || !in_array($deleter['role'], ['owner', 'admin'])) {
                throw new Exception('Unauthorized to delete users');
            }

            // Check if user being deleted is an owner
            $stmt = $this->pdo->prepare("
                SELECT role 
                FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$userIdToDelete, $orgId]);
            $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userToDelete) {
                throw new Exception('User not found');
            }

            // Only check owner count if deleting an owner
            if ($userToDelete['role'] === 'owner') {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) as owner_count 
                    FROM users 
                    WHERE org_id = ? AND role = 'owner'
                ");
                $stmt->execute([$orgId]);
                $ownerCount = $stmt->fetch(PDO::FETCH_ASSOC)['owner_count'];

                if ($ownerCount <= 1) {
                    throw new Exception('Cannot delete the last owner');
                }
            }

            // Delete user
            $stmt = $this->pdo->prepare("
                DELETE FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$userIdToDelete, $orgId]);

            return ['status' => 'success'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getOrgUsers($orgId) {
        try {
            // First verify the organization exists
            $orgStmt = $this->pdo->prepare("SELECT org_id FROM organizations WHERE org_id = ?");
            $orgStmt->execute([$orgId]);
            if (!$orgStmt->fetch()) {
                throw new Exception('Organization not found');
            }

            // Get all users for the organization
            $stmt = $this->pdo->prepare("
                SELECT 
                    user_id,
                    name,
                    email,
                    role,
                    email_verified,
                    created_at,
                    updated_at
                FROM users 
                WHERE org_id = ?
                ORDER BY created_at DESC
            ");
            
            $stmt->execute([$orgId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format the response
            $formattedUsers = array_map(function($user) {
                return [
                    'userId' => $user['user_id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'verified' => (bool)$user['email_verified'],
                    'authMethods' => [
                        'email' => true
                    ],
                    'createdAt' => date('M d, Y', strtotime($user['created_at'])),
                    'lastUpdated' => date('M d, Y', strtotime($user['updated_at']))
                ];
            }, $users);

            return [
                'status' => 'success',
                'users' => $formattedUsers,
                'totalCount' => count($users)
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function changeUserRole($orgId, $userIdToChange, $newRole, $changerId) {
        try {
            // Validate role
            $allowedRoles = ['user', 'admin', 'owner'];
            if (!in_array($newRole, $allowedRoles)) {
                throw new Exception('Invalid role. Allowed roles: ' . implode(', ', $allowedRoles));
            }

            // Check if changer has permission (must be owner)
            $stmt = $this->pdo->prepare("
                SELECT role 
                FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$changerId, $orgId]);
            $changer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$changer || $changer['role'] !== 'owner') {
                throw new Exception('Only organization owners can change user roles');
            }

            // Get current role of user being changed
            $stmt = $this->pdo->prepare("
                SELECT role 
                FROM users 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$userIdToChange, $orgId]);
            $userToChange = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userToChange) {
                throw new Exception('User not found');
            }

            // If demoting from owner, check if they're the last owner
            if ($userToChange['role'] === 'owner' && $newRole !== 'owner') {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) as owner_count 
                    FROM users 
                    WHERE org_id = ? AND role = 'owner'
                ");
                $stmt->execute([$orgId]);
                $ownerCount = $stmt->fetch(PDO::FETCH_ASSOC)['owner_count'];

                if ($ownerCount <= 1) {
                    throw new Exception('Cannot demote the last owner');
                }
            }

            // Update user role
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET role = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ? AND org_id = ?
            ");
            $stmt->execute([$newRole, $userIdToChange, $orgId]);

            return [
                'status' => 'success',
                'message' => 'User role updated successfully'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 9. Get login attempts for security monitoring (admin/owner only)
    public function getLoginAttempts($orgId, $limit = 100) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    la.email,
                    la.ip_address,
                    la.action,
                    la.success,
                    la.reason,
                    la.created_at,
                    u.user_id,
                    u.name as user_name
                FROM login_attempts la
                LEFT JOIN users u ON la.email = u.email
                WHERE u.org_id = ? OR u.org_id IS NULL
                ORDER BY la.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$orgId, $limit]);
            $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get summary statistics
            $statsStmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_attempts,
                    SUM(CASE WHEN la.success = 1 THEN 1 ELSE 0 END) as successful_attempts,
                    SUM(CASE WHEN la.success = 0 THEN 1 ELSE 0 END) as failed_attempts,
                    COUNT(DISTINCT la.ip_address) as unique_ips,
                    COUNT(DISTINCT la.email) as unique_emails
                FROM login_attempts la
                LEFT JOIN users u ON la.email = u.email
                WHERE (u.org_id = ? OR u.org_id IS NULL) 
                AND la.created_at > datetime('now', '-24 hours')
            ");
            $statsStmt->execute([$orgId]);
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'attempts' => $attempts,
                'stats_24h' => $stats
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 10. Admin reset user password (admin/owner only)
    public function adminResetPassword($adminUserId, $targetUserId, $newPassword) {
        try {
            // Validate password complexity
            $complexityErrors = $this->validatePasswordComplexity($newPassword);
            if (!empty($complexityErrors)) {
                throw new Exception('Password requirements not met: ' . implode('. ', $complexityErrors));
            }

            // Get admin user info
            $stmt = $this->pdo->prepare("SELECT role, org_id, email FROM users WHERE user_id = ?");
            $stmt->execute([$adminUserId]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                throw new Exception('Admin user not found');
            }

            // Check if admin has permission
            if (!in_array($admin['role'], ['owner', 'admin'])) {
                throw new Exception('Only admins and owners can reset user passwords');
            }

            // Get target user info and verify they're in the same org
            $stmt = $this->pdo->prepare("SELECT email, org_id, role FROM users WHERE user_id = ?");
            $stmt->execute([$targetUserId]);
            $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$targetUser) {
                throw new Exception('Target user not found');
            }

            // Verify same organization
            if ($targetUser['org_id'] !== $admin['org_id']) {
                throw new Exception('Can only reset passwords for users in your organization');
            }

            // Owners can reset anyone's password, admins cannot reset owner passwords
            if ($admin['role'] === 'admin' && $targetUser['role'] === 'owner') {
                throw new Exception('Admins cannot reset owner passwords');
            }

            // Prevent self-reset (use regular change password instead)
            if ($adminUserId === $targetUserId) {
                throw new Exception('Use change password endpoint to modify your own password');
            }

            // Check rate limiting for password resets
            $this->checkRateLimit($admin['email'], 'admin_reset_password');

            // Update password and mark for forced change on next login
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$this->hashPassword($newPassword), $targetUserId]);

            // Log the admin action
            $this->logLoginAttempt($admin['email'], 'admin_reset_password', true, "Reset password for user: {$targetUser['email']}");

            return [
                'status' => 'success', 
                'message' => 'Password reset successfully.',
                'target_user_email' => $targetUser['email']
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}