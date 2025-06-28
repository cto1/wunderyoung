<?php

header('Content-Type: application/json');

require_once 'conf.php';
require_once 'EmailTemplates.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Mailgun\Mailgun;

class UserAuthAPI {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    private function sendEmail($to, $subject, $textContent, $htmlContent) {
        $mailgunDomain = getenv('MAILGUN_DOMAIN');
        $mailgunApiKey = getenv('MAILGUN_API_KEY');
        
        if (!$mailgunDomain || !$mailgunApiKey) {
            error_log("Mailgun not configured - logging email instead");
            error_log("Email to: $to");
            error_log("Subject: $subject");
            error_log("Login link: " . strip_tags($textContent));
            return true; // Simulate successful sending in development
        }

        // Try Mailgun SDK first (more robust)
        if ($this->sendEmailViaSDK($to, $subject, $textContent, $htmlContent, $mailgunDomain, $mailgunApiKey)) {
            return true;
        }
        
        // Fallback to cURL method (your original working method)
        error_log("Falling back to cURL method for email sending");
        return $this->sendEmailViaCurl($to, $subject, $textContent, $htmlContent, $mailgunDomain, $mailgunApiKey);
    }
    
    private function sendEmailViaSDK($to, $subject, $textContent, $htmlContent, $mailgunDomain, $mailgunApiKey) {
        try {
            // Initialize Mailgun client
            $mailgun = Mailgun::create($mailgunApiKey);
            
            // Prepare email data
            $fromName = getenv('MAILGUN_FROM_NAME') ?: 'Yes Homework';
            $fromEmail = getenv('MAILGUN_FROM_EMAIL') ?: "noreply@{$mailgunDomain}";
            
            $messageData = [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => $to,
                'subject' => $subject,
                'text' => $textContent,
                'html' => $htmlContent,
                'o:tracking' => 'yes',
                'o:tracking-clicks' => 'yes',
                'o:tracking-opens' => 'yes'
            ];

            // Send the email
            $result = $mailgun->messages()->send($mailgunDomain, $messageData);
            
            if ($result->getId()) {
                error_log("Email sent successfully via SDK to: $to (Message ID: " . $result->getId() . ")");
                return true;
            } else {
                error_log("SDK: Failed to send email to: $to - No message ID returned");
                return false;
            }
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            error_log("Mailgun SDK error: " . $errorMessage);
            
            // Check for bounce-related errors
            if (strpos($errorMessage, 'bounce') !== false || 
                strpos($errorMessage, 'suppress') !== false ||
                strpos($errorMessage, '605') !== false) {
                error_log("BOUNCE DETECTED: Email $to is on bounce suppression list");
                $this->handleBouncedEmail($to, $errorMessage);
            }
            
            return false;
        }
    }
    
    private function sendEmailViaCurl($to, $subject, $textContent, $htmlContent, $mailgunDomain, $mailgunApiKey) {
        $url = "https://api.mailgun.net/v3/{$mailgunDomain}/messages";
        
        $fromName = getenv('MAILGUN_FROM_NAME') ?: 'Yes Homework';
        $fromEmail = getenv('MAILGUN_FROM_EMAIL') ?: "noreply@{$mailgunDomain}";
        
        $postData = [
            'from' => "{$fromName} <{$fromEmail}>",
            'to' => $to,
            'subject' => $subject,
            'text' => $textContent,
            'html' => $htmlContent,
            'o:tracking' => 'yes',
            'o:tracking-clicks' => 'yes',
            'o:tracking-opens' => 'yes'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgunApiKey}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("cURL error: " . $error);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            error_log("Email sent successfully via cURL to: $to");
            return true;
        } else {
            error_log("cURL: Failed to send email. HTTP Code: $httpCode, Response: $response");
            
            // Check for bounce-related errors in response
            if (strpos($response, 'bounce') !== false || 
                strpos($response, 'suppress') !== false ||
                $httpCode == 400) {
                $this->handleBouncedEmail($to, "HTTP $httpCode: $response");
            }
            
            return false;
        }
    }
    
    private function handleBouncedEmail($email, $reason) {
        // Log the bounce
        error_log("BOUNCE HANDLER: Email $email bounced - $reason");
        
        // TODO: You could implement these features:
        // 1. Mark user as having bounced email in database
        // 2. Send notification to admin about bounced emails
        // 3. Implement email verification flow
        // 4. Add to internal bounce suppression list
        
        // For now, just log it for manual review
        error_log("ACTION NEEDED: Review bounced email address: $email");
    }

    private function sendLoginEmail($email, $token) {
        $loginLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($email);
        error_log('Login link: ' . $loginLink);
        
        require_once 'EmailTemplates.php';
        $emailTemplates = new EmailTemplates();
        $template = $emailTemplates->getLoginEmail($email, $loginLink);
        
        return $this->sendEmail($email, $template['subject'], $template['text'], $template['html']);
    }

    private function sendWelcomeEmail($email, $token) {
        $loginLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($email);
        
        require_once 'EmailTemplates.php';
        $emailTemplates = new EmailTemplates();
        $template = $emailTemplates->getWelcomeEmail($email, $loginLink);
        
        return $this->sendEmail($email, $template['subject'], $template['text'], $template['html']);
    }

    // 1. Sign up with email (and optionally password)
    public function signup($data) {
        try {
            if (!isset($data['email'])) {
                throw new Exception('Email is required');
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            // Validate password if provided (for traditional signup)
            $passwordHash = null;
            if (isset($data['password']) && !empty($data['password'])) {
                if (strlen($data['password']) < 8) {
                    throw new Exception('Password must be at least 8 characters long');
                }
                $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $this->pdo->beginTransaction();
                
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                throw new Exception('User already exists');
            }

            // Generate user ID and create user
            $userId = Database::generateUserId();
            $stmt = $this->pdo->prepare("
                INSERT INTO users (id, email, password_hash, plan, is_verified) 
                VALUES (?, ?, ?, 'free', 0)
            ");
            
            $stmt->execute([$userId, $data['email'], $passwordHash]);

            // Generate welcome token for immediate access
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours')); // 24 hour expiry for welcome email

            // Save welcome token
            $stmt = $this->pdo->prepare("
                INSERT INTO magic_links (user_id, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $token, $expires]);

            $this->pdo->commit();

            // Send welcome email with login link
            $emailSent = $this->sendWelcomeEmail($data['email'], $token);
            if (!$emailSent) {
                error_log("Failed to send welcome email to: " . $data['email']);
                // Don't fail signup if email fails - user can still request login later
            }

            // Customize message based on email delivery and auth method
            $message = '';
            if ($passwordHash) {
                if ($emailSent) {
                    $message = 'Account created successfully! You can login with your password or check your email for a magic link.';
                } else {
                    $message = 'Account created successfully! Please login with your password below. (Email delivery failed - you can request a new login link later.)';
                }
            } else {
                if ($emailSent) {
                    $message = 'Account created successfully! Check your email for getting started instructions.';
                } else {
                    $message = 'Account created, but email delivery failed. Please contact support or try signing up with a different email address.';
                }
            }

            return [
                'status' => 'success', 
                'user_id' => $userId,
                'message' => $message
            ];

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
            $stmt = $this->pdo->prepare("SELECT id, is_verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found');
            }

            // Generate login token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Clear old tokens for this user
            $stmt = $this->pdo->prepare("DELETE FROM magic_links WHERE user_id = ?");
            $stmt->execute([$user['id']]);

            // Save new token
            $stmt = $this->pdo->prepare("
                INSERT INTO magic_links (user_id, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user['id'], $token, $expires]);

            // Send email with login link
            if (!$this->sendLoginEmail($email, $token)) {
                throw new Exception('Failed to send login email');
            }

            return [
                'status' => 'success',
                'message' => 'Login link sent to email',
                'user_id' => $user['id'],
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
                SELECT u.id, u.email, u.plan, u.is_verified
                FROM users u
                JOIN magic_links ml ON u.id = ml.user_id
                WHERE u.email = ? 
                AND ml.token = ? 
                AND ml.expires_at > CURRENT_TIMESTAMP
            ");
            $stmt->execute([$email, $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                // Check if user exists but token is invalid/expired
                $userCheck = $this->pdo->prepare("SELECT email, is_verified FROM users WHERE email = ?");
                $userCheck->execute([$email]);
                $existingUser = $userCheck->fetch(PDO::FETCH_ASSOC);
                
                if ($existingUser && $existingUser['is_verified']) {
                    throw new Exception('This login link has already been used. Please request a new login link.');
                } else if ($existingUser) {
                    throw new Exception('Login link has expired. Please request a new login link.');
                } else {
                    throw new Exception('Invalid login link. Please check your email or request a new login link.');
                }
            }

            // Mark user as verified and clear the token
            $stmt = $this->pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Clear used token
            $stmt = $this->pdo->prepare("DELETE FROM magic_links WHERE user_id = ? AND token = ?");
            $stmt->execute([$user['id'], $token]);
    
            return [
                'status' => 'success',
                'id' => $user['id'],
                'email' => $user['email'],
                'plan' => $user['plan'],
                'is_verified' => 1
            ];
    
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 4. Password login
    public function passwordLogin($email, $password) {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            if (empty($password)) {
                throw new Exception('Password is required');
            }

            // Find user and verify password
            $stmt = $this->pdo->prepare("SELECT id, email, password_hash, plan, is_verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('Invalid email or password');
            }

            // Check if user has a password set
            if (empty($user['password_hash'])) {
                throw new Exception('This account uses passwordless login. Please request a magic link instead.');
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid email or password');
            }

            // Mark user as verified (since they successfully logged in with password)
            if (!$user['is_verified']) {
                $stmt = $this->pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                $stmt->execute([$user['id']]);
                $user['is_verified'] = 1;
            }

            return [
                'status' => 'success',
                'id' => $user['id'],
                'email' => $user['email'],
                'plan' => $user['plan'],
                'is_verified' => $user['is_verified']
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 5. Get user profile
    public function getProfile($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, plan, is_verified, 
                       stripe_customer_id, stripe_subscription_id, 
                       plan_ends_at, created_at 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found');
            }

            return [
                'status' => 'success',
                'user' => $user
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 5. Update user profile
    public function updateProfile($userId, $data) {
        try {
            $updates = [];
            $params = [':id' => $userId];

            if (isset($data['email'])) {
                $updates[] = "email = :email";
                $params[':email'] = $data['email'];
                // Reset verification if email changed
                $updates[] = "is_verified = 0";
            }

            if (isset($data['plan'])) {
                $updates[] = "plan = :plan";
                $params[':plan'] = $data['plan'];
            }

            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ['status' => 'success', 'message' => 'Profile updated successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 6. Add child
    public function addChild($userId, $data) {
        try {
            if (!isset($data['name']) || !isset($data['age_group'])) {
                throw new Exception('Name and age group are required');
            }

            // Generate child ID
            $childId = Database::generateChildId();
            $stmt = $this->pdo->prepare("
                INSERT INTO children (id, user_id, name, age_group, interest1, interest2) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $childId,
                $userId,
                $data['name'],
                $data['age_group'],
                $data['interest1'] ?? null,
                $data['interest2'] ?? null
            ]);

            return [
                'status' => 'success',
                'child_id' => $childId,
                'message' => 'Child added successfully'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 7. Get user's children
    public function getChildren($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, age_group, interest1, interest2, created_at 
                FROM children 
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'children' => $children
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 8. Update child
    public function updateChild($userId, $childId, $data) {
        try {
            // Verify child belongs to user
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $userId]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }

            $updates = [];
            $params = [':id' => $childId];

            if (isset($data['name'])) {
                $updates[] = "name = :name";
                $params[':name'] = $data['name'];
            }

            if (isset($data['age_group'])) {
                $updates[] = "age_group = :age_group";
                $params[':age_group'] = $data['age_group'];
            }

            if (isset($data['interest1'])) {
                $updates[] = "interest1 = :interest1";
                $params[':interest1'] = $data['interest1'];
            }

            if (isset($data['interest2'])) {
                $updates[] = "interest2 = :interest2";
                $params[':interest2'] = $data['interest2'];
            }

            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $sql = "UPDATE children SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ['status' => 'success', 'message' => 'Child updated successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 9. Delete child
    public function deleteChild($userId, $childId) {
        try {
            // Verify child belongs to user
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $userId]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }

            $stmt = $this->pdo->prepare("DELETE FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $userId]);

            return ['status' => 'success', 'message' => 'Child deleted successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}