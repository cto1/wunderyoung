<?php

header('Content-Type: application/json');

require_once 'conf.php';
require_once 'EmailTemplates.php';

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
            return true; // Simulate successful sending
        }

        $url = "https://api.mailgun.net/v3/{$mailgunDomain}/messages";
        
        $postData = [
            'from' => "Daily Homework <noreply@{$mailgunDomain}>",
            'to' => $to,
            'subject' => $subject,
            'text' => $textContent,
            'html' => $htmlContent
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
            error_log("Curl error: " . $error);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            error_log("Email sent successfully to: $to");
            return true;
        } else {
            error_log("Failed to send email. HTTP Code: $httpCode, Response: $response");
            return false;
        }
    }

    private function sendLoginEmail($email, $token) {
        $loginLink = getenv('APP_URL') . "/verify.php?token=" . $token . "&email=" . urlencode($email);
        error_log('Login link: ' . $loginLink);
        $subject = "Your Daily Homework Login Link";
        $textContent = "Click here to login: " . $loginLink;
        $htmlContent = "<p>Click <a href='" . $loginLink . "'>here to login</a> to Daily Homework.</p>";
        return $this->sendEmail($email, $subject, $textContent, $htmlContent);
    }

    // 1. Sign up with email
    public function signup($data) {
        try {
            if (!isset($data['email'])) {
                throw new Exception('Email is required');
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            $this->pdo->beginTransaction();
                
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                throw new Exception('User already exists');
            }

            // Create user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, plan, is_verified) 
                VALUES (?, 'free', 0)
            ");
            
            $stmt->execute([$data['email']]);
            $userId = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return ['status' => 'success', 'user_id' => $userId];

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
                throw new Exception('Invalid or expired token');
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

    // 4. Get user profile
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

            $stmt = $this->pdo->prepare("
                INSERT INTO children (user_id, name, age_group, interest1, interest2) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $data['name'],
                $data['age_group'],
                $data['interest1'] ?? null,
                $data['interest2'] ?? null
            ]);

            $childId = $this->pdo->lastInsertId();

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