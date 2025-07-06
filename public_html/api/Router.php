<?php

class Router {
    private $routes = [];
    private $publicRoutes = ['/health', '/auth/signup', '/auth/login', '/auth/magic-link', '/auth/magic-login', '/worksheets/pdf'];
    
    public function __construct() {
        $this->setupRoutes();
    }
    
    private function setupRoutes() {
        // Public routes
        $this->routes['GET']['/health'] = 'health';
        
        // Auth routes
        $this->routes['POST']['/auth/signup'] = 'auth_signup';
        $this->routes['POST']['/auth/login'] = 'auth_login';
        $this->routes['POST']['/auth/verify'] = 'auth_verify';
        $this->routes['POST']['/auth/magic-link'] = 'auth_magic_link';
        $this->routes['POST']['/auth/magic-login'] = 'auth_magic_login';
        
        // Children routes
        $this->routes['GET']['/children'] = 'children_get';
        $this->routes['POST']['/children'] = 'children_create';
        $this->routes['PUT']['/children'] = 'children_update';
        $this->routes['DELETE']['/children'] = 'children_delete';
        
        // Worksheet routes
        $this->routes['GET']['/worksheets'] = 'worksheets_list';
        $this->routes['POST']['/worksheets/generate'] = 'worksheets_generate';
        $this->routes['POST']['/worksheets/pdf'] = 'worksheets_create_pdf';
        $this->routes['GET']['/worksheets/pdf'] = 'worksheets_pdf';
        
        // Feedback routes
        $this->routes['POST']['/feedback'] = 'feedback_submit';
        $this->routes['GET']['/feedback'] = 'feedback_get';
        
        // Email routes
        $this->routes['POST']['/email/send'] = 'email_send';
    }
    
    public function route($method, $path) {
        // Check if route exists
        if (!isset($this->routes[$method][$path])) {
            return ['error' => 'Endpoint not found', 'code' => 404];
        }
        
        // Check if authentication is required
        if (!in_array($path, $this->publicRoutes)) {
            if (!$this->isAuthenticated()) {
                return ['error' => 'Authentication required', 'code' => 401];
            }
        }
        
        return ['handler' => $this->routes[$method][$path], 'code' => 200];
    }
    
    public function isAuthenticated() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return false;
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        require_once __DIR__ . '/UserAuthAPI.php';
        $auth = new UserAuthAPI();
        return $auth->verifyToken($token) !== false;
    }
    
    public function getPublicRoutes() {
        return $this->publicRoutes;
    }
}