<?php
// Router.php
require_once 'AuthMiddleware.php';

class Router {
    private $routes = [];
    private $basePath = '/api';
    private $authMiddleware;

    public function __construct() {
        $this->authMiddleware = new AuthMiddleware();
    }

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->buildPattern($path)
        ];
    }

    private function buildPattern($path) {
        // Convert {param} to named capture groups
        return '#^' . $this->basePath . preg_replace('/{([a-zA-Z0-9_]+)}/', '(?P<$1>[^/]+)', $path) . '$#';
    }

    public function handle($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $input = json_decode(file_get_contents('php://input'), true);
        $userData = null;
        $params = [];
        $statusCode = 200;

        try {
            // Authenticate the request
            $userData = $this->authMiddleware->authenticate($method, $uri);
            
            // Process the route
            foreach ($this->routes as $route) {
                if ($method !== $route['method']) {
                    continue;
                }

                if (preg_match($route['pattern'], $uri, $matches)) {
                    // Remove numeric keys from matches
                    $params = array_filter($matches, function($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);

                    // Add user data to the handler context
                    $handlerContext = ['userData' => $userData];

                    try {
                        $response = $route['handler']($params, $input, $handlerContext);
                        
                        return $response;
                    } catch (Exception $e) {
                        $statusCode = 500;
                        http_response_code(500);
                        $response = [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                        
                        return $response;
                    }
                }
            }

            $statusCode = 404;
            http_response_code(404);
            $response = ['status' => 'error', 'message' => 'Route not found'];
            
            return $response;
            
        } catch (Exception $e) {
            $statusCode = 401;
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            
            return $response;
        }
    }
}