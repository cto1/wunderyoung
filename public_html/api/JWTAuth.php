<?php
// JWTAuth.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth {
    private $secretKey;
    private $algorithm;
    private $tokenExpiration; // in seconds
    private $db;
    private $pdo;

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET_KEY');
        if (!$this->secretKey) {
            error_log('JWT_SECRET_KEY is not set in environment variables. This is a critical security issue.');
            throw new \Exception('JWT configuration error. Check server logs.');
        }
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
                'user_id' => $userData['user_id'],
                'org_id' => $userData['org_id'],
                'email' => $userData['email'],
                'role' => $userData['role']
            ]
        ];
        
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate a JWT token
     * 
     * @param string $token The JWT token to validate
     * @return array|false Decoded token data or false if invalid
     */
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded->data;
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
            SELECT user_id, org_id, email, role 
            FROM users 
            WHERE user_id = ?
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
        $userData = $this->getUserData($userData['user_id']);
        
        if (!$userData) {
            return false;
        }
        
        // Generate new token with current data
        return $this->generateToken($userData);
    }
}