<?php
// AuthMiddleware.php

require_once 'JWTAuth.php';

class AuthMiddleware {
    private $jwtAuth;
    private $publicRoutes;

    public function __construct() {
        $this->jwtAuth = new JWTAuth();
        
        // Define routes that don't require authentication
        $this->publicRoutes = [
            // Auth endpoints
            ['method' => 'POST', 'path' => '/api/auth/signup'],
            ['method' => 'POST', 'path' => '/api/auth/login'],
            ['method' => 'GET', 'path' => '/api/auth/verify'],
            ['method' => 'POST', 'path' => '/api/auth/token'],
            
            // Public health check
            ['method' => 'GET', 'path' => '/api/health'],
            
            // Debug endpoints
            ['method' => 'GET', 'path' => '/api/debug/env'],
        ];
    }

    /**
     * Check if a route is public
     * 
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return bool True if the route is public
     */
    public function isPublicRoute($method, $uri) {
        foreach ($this->publicRoutes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (isset($route['path']) && $route['path'] === $uri) {
                return true;
            }
            
            if (isset($route['pathPattern']) && preg_match($route['pathPattern'], $uri)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Authenticate a request
     * 
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|null User data if authenticated, null if public route, or throws exception if auth fails
     */
    public function authenticate($method, $uri) {
        // Skip authentication for public routes
        if ($this->isPublicRoute($method, $uri)) {
            return null;
        }
        
        // Get token from header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        // Check if token exists
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            throw new Exception('Authorization token not found');
        }
        
        $token = $matches[1];
        $userData = $this->jwtAuth->validateToken($token);
        
        // Check if token is valid
        if (!$userData) {
            http_response_code(401);
            throw new Exception('Invalid or expired token');
        }
        
        return $userData;
    }
}