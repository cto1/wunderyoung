<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/Router.php';

try {
    $router = new Router();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove /api prefix if present
    $path = preg_replace('/^\/api/', '', $path);
    
    // Default to root if empty
    if (empty($path) || $path === '/') {
        $path = '/health';
    }
    
    $routeResult = $router->route($method, $path);
    
    if (isset($routeResult['error'])) {
        http_response_code($routeResult['code']);
        echo json_encode(['error' => $routeResult['error']]);
        exit;
    }
    
    $handler = $routeResult['handler'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($handler) {
        case 'health':
            echo json_encode([
                'status' => 'API is running',
                'timestamp' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ]);
            break;
            
        case 'auth_signup':
            require_once __DIR__ . '/UserAuthAPI.php';
            $api = new UserAuthAPI();
            
            $email = $input['email'] ?? null;
            $password = $input['password'] ?? null;
            $name = $input['name'] ?? null;
            
            if (!$email || !$password || !$name) {
                http_response_code(400);
                echo json_encode(['error' => 'email, password, and name are required']);
                exit;
            }
            
            $result = $api->signup($email, $password, $name);
            echo json_encode($result);
            break;
            
        case 'auth_login':
            require_once __DIR__ . '/UserAuthAPI.php';
            $api = new UserAuthAPI();
            
            $email = $input['email'] ?? null;
            $password = $input['password'] ?? null;
            
            if (!$email || !$password) {
                http_response_code(400);
                echo json_encode(['error' => 'email and password are required']);
                exit;
            }
            
            $result = $api->login($email, $password);
            echo json_encode($result);
            break;
            
        case 'auth_verify':
            require_once __DIR__ . '/UserAuthAPI.php';
            $api = new UserAuthAPI();
            
            $token = $input['token'] ?? null;
            if (!$token) {
                http_response_code(400);
                echo json_encode(['error' => 'token is required']);
                exit;
            }
            
            $result = $api->verifyToken($token);
            echo json_encode($result ? ['status' => 'valid'] : ['status' => 'invalid']);
            break;
            
        case 'children_get':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            $result = $api->getChildren();
            echo json_encode($result);
            break;
            
        case 'children_create':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            $result = $api->createChild();
            echo json_encode($result);
            break;
            
        case 'children_update':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            $result = $api->updateChild();
            echo json_encode($result);
            break;
            
        case 'children_delete':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            $result = $api->deleteChild();
            echo json_encode($result);
            break;
            
        case 'worksheets_generate':
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            $result = $api->generateWorksheet();
            echo json_encode($result);
            break;
            
        case 'worksheets_pdf':
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            $result = $api->generatePDF();
            echo json_encode($result);
            break;
            
        case 'feedback_submit':
            require_once __DIR__ . '/FeedbackAPI.php';
            $api = new FeedbackAPI();
            $result = $api->submitFeedback();
            echo json_encode($result);
            break;
            
        case 'feedback_get':
            require_once __DIR__ . '/FeedbackAPI.php';
            $api = new FeedbackAPI();
            $result = $api->getFeedback();
            echo json_encode($result);
            break;
            
        case 'email_send':
            require_once __DIR__ . '/EmailAPI.php';
            $api = new EmailAPI();
            $result = $api->sendEmail();
            echo json_encode($result);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Handler not found']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}