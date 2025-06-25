<?php
// JWTAuth.php - Native PHP JWT Implementation

class JWTAuth {
    private $secretKey;
    private $algorithm;
    private $tokenExpiration; // in seconds
    private $db;
    private $pdo;

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'your-secret-key-change-this-in-production';
        $this->algorithm = 'HS256';
        $this->tokenExpiration = getenv('JWT_EXPIRATION') ? (int)getenv('JWT_EXPIRATION') : 86400; // 24 hours
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    /**
     * Generate a JWT for a user
     * 
     * @param array $userData User data to encode in the token
     * @return string The JWT token
     */
    public function generateToken($userData) {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->tokenExpiration;
        
        $payload = [
            'iat' => $issuedAt,     // Issued at time
            'exp' => $expiresAt,    // Expiration time
            'data' => [
                'id' => $userData['id'],
                'email' => $userData['email'],
                'plan' => $userData['plan'] ?? 'free'
            ]
        ];
        
        return $this->encodeJWT($payload);
    }

    /**
     * Validate a JWT token
     * 
     * @param string $token The JWT token to validate
     * @return array|false Decoded token data or false if invalid
     */
    public function validateToken($token) {
        try {
            $decoded = $this->decodeJWT($token);
            
            // Check if token has expired
            if (isset($decoded['exp']) && $decoded['exp'] < time()) {
                error_log('JWT token has expired');
                return false;
            }
            
            return $decoded['data'] ?? false;
        } catch (\Exception $e) {
            error_log('JWT validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user data from the database for token generation
     * 
     * @param string $userId The user ID
     * @return array|false User data or false if not found
     */
    public function getUserData($userId) {
        $stmt = $this->pdo->prepare("
            SELECT id, email, plan 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Refresh a token that's about to expire
     * 
     * @param string $token The current token
     * @return string|false New token or false if current token is invalid
     */
    public function refreshToken($token) {
        $userData = $this->validateToken($token);
        if (!$userData) {
            return false;
        }
        
        // Verify user still exists and data is current
        $userData = $this->getUserData($userData['id']);
        
        if (!$userData) {
            return false;
        }
        
        // Generate new token with current data
        return $this->generateToken($userData);
    }

    /**
     * Native PHP JWT encoding
     * 
     * @param array $payload The payload to encode
     * @return string The JWT token
     */
    private function encodeJWT($payload) {
        // Create header
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];
        
        // Encode header and payload
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        // Create signature
        $signature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);
        
        // Return complete JWT
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }

    /**
     * Native PHP JWT decoding
     * 
     * @param string $jwt The JWT token to decode
     * @return array The decoded payload
     * @throws Exception If the token is invalid
     */
    private function decodeJWT($jwt) {
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            throw new \Exception('Invalid JWT format');
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verify signature
        $expectedSignature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            throw new \Exception('Invalid JWT signature');
        }
        
        // Decode header and payload
        $header = json_decode($this->base64UrlDecode($headerEncoded), true);
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        
        if (!$header || !$payload) {
            throw new \Exception('Invalid JWT data');
        }
        
        // Verify algorithm
        if ($header['alg'] !== $this->algorithm) {
            throw new \Exception('Invalid JWT algorithm');
        }
        
        return $payload;
    }

    /**
     * Create HMAC signature for JWT
     * 
     * @param string $data The data to sign
     * @return string The base64url encoded signature
     */
    private function createSignature($data) {
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        return $this->base64UrlEncode($signature);
    }

    /**
     * Base64 URL-safe encoding
     * 
     * @param string $data The data to encode
     * @return string The encoded data
     */
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL-safe decoding
     * 
     * @param string $data The data to decode
     * @return string The decoded data
     */
    private function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}