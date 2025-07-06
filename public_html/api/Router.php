<?php

class Router {
    private $routes = [];
    private $publicRoutes = ['/health', '/auth/signup', '/auth/login'];
    
    public function __construct() {
        $this->setupRoutes();
    }
    
    private function setupRoutes() {
        // Public routes
        $this->routes['GET']['/health'] = [$this, 'health'];
        
        // Protected routes
        $this->routes['POST']['/auth/signup'] = ['UserAuthAPI', 'signup'];
        $this->routes['POST']['/auth/login'] = ['UserAuthAPI', 'login'];
        $this->routes['POST']['/auth/verify'] = ['UserAuthAPI', 'verifyToken'];
        
        $this->routes['GET']['/children'] = ['ChildAPI', 'getChildren'];
        $this->routes['POST']['/children'] = ['ChildAPI', 'createChild'];
        $this->routes['PUT']['/children'] = ['ChildAPI', 'updateChild'];
        $this->routes['DELETE']['/children'] = ['ChildAPI', 'deleteChild'];
        
        $this->routes['POST']['/worksheets/generate'] = ['SimpleWorksheetAPI', 'generateWorksheet'];
        $this->routes['GET']['/worksheets/pdf'] = ['SimpleWorksheetAPI', 'generatePDF'];
        
        $this->routes['POST']['/feedback'] = ['FeedbackAPI', 'submitFeedback'];
        $this->routes['GET']['/feedback'] = ['FeedbackAPI', 'getFeedback'];
        
        $this->routes['POST']['/email/send'] = ['EmailAPI', 'sendEmail'];
    }
    
    public function route($method, $path) {
        // Check if route exists
        if (!isset($this->routes[$method][$path])) {
            $this->sendError(404, 'Endpoint not found');
            return;
        }
        
        // Check if authentication is required
        if (!in_array($path, $this->publicRoutes)) {
            if (!$this->isAuthenticated()) {
                $this->sendError(401, 'Authentication required');
                return;
            }
        }
        
        $handler = $this->routes[$method][$path];
        
        // Handle built-in methods
        if (is_array($handler) && $handler[0] === $this) {
            call_user_func($handler);
            return;
        }
        
        // Handle API class methods
        if (is_array($handler)) {
            $className = $handler[0];
            $methodName = $handler[1];
            
            require_once __DIR__ . '/' . $className . '.php';
            $api = new $className();
            
            if (method_exists($api, $methodName)) {
                $this->callApiMethod($api, $methodName, $path);
            } else {
                $this->sendError(500, 'Method not found');
            }
        }
    }
    
    private function isAuthenticated() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return false;
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        require_once __DIR__ . '/UserAuthAPI.php';
        $auth = new UserAuthAPI();
        return $auth->verifyJWT($token);
    }
    
    private function health() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get()
        ]);
    }
    
    private function sendError($code, $message) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }
}