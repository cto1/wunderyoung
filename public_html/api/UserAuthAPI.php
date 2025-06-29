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