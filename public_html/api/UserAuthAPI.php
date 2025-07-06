<?php
require_once 'conf.php';
require_once 'env.php';
loadEnv();

class UserAuthAPI {
    private $pdo;
    private $jwtSecret;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-this';
    }
    
    /**
     * User signup
     * POST /api/UserAuthAPI.php?action=signup
     */
    public function signup($email, $password, $name) {
        try {
            // Validate input
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            if (strlen($password) < 6) {
                throw new Exception('Password must be at least 6 characters');
            }
            
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('User already exists');
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Create user
            $userId = Database::generateUserId();
            $stmt = $this->pdo->prepare("
                INSERT INTO users (id, email, name, password_hash, plan, created_at) 
                VALUES (?, ?, ?, ?, 'free', CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$userId, $email, $name, $hashedPassword]);
            
            // Generate JWT token
            $token = $this->generateJWT($userId, $email);
            
            return [
                'status' => 'success',
                'user_id' => $userId,
                'token' => $token,
                'message' => 'User created successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * User login
     * POST /api/UserAuthAPI.php?action=login
     */
    public function login($email, $password) {
        try {
            // Get user
            $stmt = $this->pdo->prepare("SELECT id, email, name, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('Invalid credentials');
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid credentials');
            }
            
            // Generate JWT token
            $token = $this->generateJWT($user['id'], $user['email']);
            
            return [
                'status' => 'success',
                'user_id' => $user['id'],
                'token' => $token,
                'name' => $user['name'],
                'message' => 'Login successful'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                throw new Exception('Invalid token format');
            }
            
            $payload = json_decode(base64_decode($parts[1]), true);
            if (!$payload) {
                throw new Exception('Invalid token payload');
            }
            
            // Check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                throw new Exception('Token expired');
            }
            
            // Verify signature
            $signature = base64_encode(hash_hmac('sha256', $parts[0] . '.' . $parts[1], $this->jwtSecret, true));
            if ($signature !== $parts[2]) {
                throw new Exception('Invalid token signature');
            }
            
            return $payload;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get user info from token
     */
    public function getUserFromToken($token) {
        $payload = $this->verifyToken($token);
        if (!$payload) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("SELECT id, email, name, plan FROM users WHERE id = ?");
        $stmt->execute([$payload['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Passwordless signup - create account with just email, send magic link
     * POST /api/UserAuthAPI.php?action=passwordless-signup
     */
    public function passwordlessSignup($email) {
        try {
            // Validate input
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                // User exists, just send magic link
                return $this->requestMagicLink($email);
            }
            
            // Create user without password or name
            $userId = Database::generateUserId();
            $stmt = $this->pdo->prepare("
                INSERT INTO users (id, email, password_hash, plan, created_at) 
                VALUES (?, ?, NULL, 'free', CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$userId, $email]);
            
            // Generate and send magic link
            $magicToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 minutes
            
            // Store magic token
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET magic_token = ?, magic_expires_at = ? 
                WHERE id = ?
            ");
            $stmt->execute([$magicToken, $expiresAt, $userId]);
            
            // Send magic link email
            $this->sendMagicLinkEmail($email, $email, $magicToken);
            
            return [
                'status' => 'success',
                'user_id' => $userId,
                'message' => 'Account created! Magic link sent to your email address'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send magic login link via email
     * POST /api/UserAuthAPI.php?action=request-magic-link
     */
    public function requestMagicLink($email) {
        try {
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('No account found with this email address');
            }
            
            // Generate magic link token (valid for 15 minutes)
            $magicToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 minutes
            
            // Store magic token in users table
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET magic_token = ?, magic_expires_at = ? 
                WHERE id = ?
            ");
            $stmt->execute([$magicToken, $expiresAt, $user['id']]);
            
            // Send magic link email
            $this->sendMagicLinkEmail($user['email'], $user['email'], $magicToken);
            
            return [
                'status' => 'success',
                'message' => 'Magic link sent to your email address'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Login with magic link token
     * POST /api/UserAuthAPI.php?action=magic-login
     */
    public function magicLogin($magicToken) {
        try {
            // Find valid magic link
            $stmt = $this->pdo->prepare("
                SELECT id, email 
                FROM users 
                WHERE magic_token = ? AND magic_expires_at > datetime('now')
            ");
            $stmt->execute([$magicToken]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                throw new Exception('Invalid or expired magic link');
            }
            
            // Clear magic token after use
            $stmt = $this->pdo->prepare("UPDATE users SET magic_token = NULL, magic_expires_at = NULL WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            // Generate JWT token
            $token = $this->generateJWT($data['id'], $data['email']);
            
            return [
                'status' => 'success',
                'user_id' => $data['id'],
                'token' => $token,
                'message' => 'Login successful'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function generateJWT($userId, $email) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userId,
            'email' => $email,
            'exp' => time() + (7 * 24 * 60 * 60) // 7 days
        ]);
        
        $base64Header = base64_encode($header);
        $base64Payload = base64_encode($payload);
        
        $signature = base64_encode(hash_hmac('sha256', $base64Header . '.' . $base64Payload, $this->jwtSecret, true));
        
        return $base64Header . '.' . $base64Payload . '.' . $signature;
    }
    
    private function sendMagicLinkEmail($email, $name, $magicToken) {
        $domain = $_ENV['MAILGUN_DOMAIN'] ?? '';
        $apiKey = $_ENV['MAILGUN_API_KEY'] ?? '';
        
        if (!$domain || !$apiKey) {
            throw new Exception('Mailgun configuration missing');
        }
        
        $magicUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                    '://' . $_SERVER['HTTP_HOST'] . '/auth/magic-login?token=' . $magicToken;
        
        $subject = 'Your Magic Login Link - Wunderyoung';
        $message = $this->buildMagicLinkEmail($name, $magicUrl);
        
        $url = "https://api.mailgun.net/v3/{$domain}/messages";
        $data = [
            'from' => "Wunderyoung <noreply@{$domain}>",
            'to' => $email,
            'subject' => $subject,
            'html' => $message
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "api:{$apiKey}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Failed to send magic link email');
        }
    }
    
    private function buildMagicLinkEmail($name, $magicUrl) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc;'>
                <div style='background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <h1 style='color: #2563eb; margin: 0; font-size: 28px;'>✨ Wunderyoung</h1>
                        <p style='color: #64748b; margin: 5px 0 0 0; font-size: 14px;'>Where Learning Meets Wonder</p>
                    </div>
                    
                    <h2 style='color: #1e293b; margin-bottom: 20px;'>🔐 Your Magic Login Link</h2>
                    
                    <p>Hello <strong>{$name}</strong>!</p>
                    
                    <p>Click the button below to securely log into your Wunderyoung account. This link will expire in 15 minutes for your security.</p>
                    
                    <div style='text-align: center; margin: 40px 0;'>
                        <a href='{$magicUrl}' style='
                            background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
                            color: white; 
                            padding: 15px 30px; 
                            text-decoration: none; 
                            border-radius: 8px; 
                            display: inline-block; 
                            font-weight: bold; 
                            font-size: 16px;
                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                        '>
                            🚀 Login to Wunderyoung
                        </a>
                    </div>
                    
                    <div style='background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                        <p style='margin: 0; font-size: 14px; color: #475569;'>
                            <strong>Security Note:</strong> If you didn't request this login link, you can safely ignore this email. 
                            The link will expire automatically.
                        </p>
                    </div>
                    
                    <p>Happy learning!</p>
                    <p style='margin-bottom: 0;'>
                        <strong>The Wunderyoung Team</strong><br>
                        <small style='color: #64748b;'>Making education magical, one worksheet at a time</small>
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }
}

// Handle direct API calls
if (basename($_SERVER['SCRIPT_NAME']) === 'UserAuthAPI.php') {
    $api = new UserAuthAPI();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'signup':
                    $email = $input['email'] ?? null;
                    $password = $input['password'] ?? null;
                    $name = $input['name'] ?? null;
                    
                    if (!$email || !$password || !$name) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'email, password, and name are required']);
                        exit;
                    }
                    
                    $result = $api->signup($email, $password, $name);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'login':
                    $email = $input['email'] ?? null;
                    $password = $input['password'] ?? null;
                    
                    if (!$email || !$password) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'email and password are required']);
                        exit;
                    }
                    
                    $result = $api->login($email, $password);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'request-magic-link':
                    $email = $input['email'] ?? null;
                    
                    if (!$email) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'email is required']);
                        exit;
                    }
                    
                    $result = $api->requestMagicLink($email);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'magic-login':
                    $magicToken = $input['token'] ?? null;
                    
                    if (!$magicToken) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'token is required']);
                        exit;
                    }
                    
                    $result = $api->magicLogin($magicToken);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'passwordless-signup':
                    $email = $input['email'] ?? null;
                    
                    if (!$email) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'email is required']);
                        exit;
                    }
                    
                    $result = $api->passwordlessSignup($email);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Action parameter required']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
}
?> 