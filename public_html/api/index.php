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
        echo json_encode(['error' => $routeResult['error'], 'debug_method' => $method, 'debug_path' => $path]);
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
            
            $name = $input['name'] ?? null;
            $ageGroup = $input['age_group'] ?? null;
            $interest1 = $input['interest1'] ?? null;
            $interest2 = $input['interest2'] ?? '';
            
            if (!$name || !$ageGroup || !$interest1) {
                http_response_code(400);
                echo json_encode(['error' => 'name, age_group, and interest1 are required']);
                exit;
            }
            
            $result = $api->addChild($name, $ageGroup, $interest1, $interest2);
            echo json_encode($result);
            break;
            
        case 'children_update':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            
            $childId = $input['child_id'] ?? null;
            $name = $input['name'] ?? null;
            $ageGroup = $input['age_group'] ?? null;
            $interest1 = $input['interest1'] ?? null;
            $interest2 = $input['interest2'] ?? '';
            
            if (!$childId || !$name || !$ageGroup || !$interest1) {
                http_response_code(400);
                echo json_encode(['error' => 'child_id, name, age_group, and interest1 are required']);
                exit;
            }
            
            $result = $api->updateChild($childId, $name, $ageGroup, $interest1, $interest2);
            echo json_encode($result);
            break;
            
        case 'children_delete':
            require_once __DIR__ . '/ChildAPI.php';
            $api = new ChildAPI();
            
            $childId = $input['child_id'] ?? null;
            if (!$childId) {
                http_response_code(400);
                echo json_encode(['error' => 'child_id is required']);
                exit;
            }
            
            $result = $api->deleteChild($childId);
            echo json_encode($result);
            break;
            
        case 'worksheets_list':
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            $result = $api->getWorksheets();
            echo json_encode($result);
            break;
            
        case 'worksheets_generate':
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            
            $childId = $input['child_id'] ?? null;
            $date = $input['date'] ?? null;
            
            if (!$childId) {
                http_response_code(400);
                echo json_encode(['error' => 'child_id is required']);
                exit;
            }
            
            $result = $api->generateContent($childId, $date);
            echo json_encode($result);
            break;
            
        case 'worksheets_create_pdf':
            $worksheetId = $input['worksheet_id'] ?? null;
            if (!$worksheetId) {
                http_response_code(400);
                echo json_encode(['error' => 'worksheet_id is required']);
                exit;
            }
            
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            
            try {
                $result = $api->createPDF($worksheetId);
                echo json_encode($result);
            } catch (Exception $e) {
                echo json_encode(['error' => 'Exception caught', 'message' => $e->getMessage()]);
            } catch (Error $e) {
                echo json_encode(['error' => 'Fatal error caught', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'worksheets_pdf':
            require_once __DIR__ . '/SimpleWorksheetAPI.php';
            $api = new SimpleWorksheetAPI();
            
            $worksheetId = $_GET['worksheet_id'] ?? null;
            if (!$worksheetId) {
                http_response_code(400);
                echo json_encode(['error' => 'worksheet_id is required']);
                exit;
            }
            
            // downloadPDF outputs the PDF directly, no JSON response
            $api->downloadPDF($worksheetId);
            break;
            
        case 'feedback_submit':
            require_once __DIR__ . '/FeedbackAPI.php';
            $api = new FeedbackAPI();
            
            $token = $input['token'] ?? null;
            $parentName = $input['parentName'] ?? null;
            $parentEmail = $input['parentEmail'] ?? null;
            $difficulty = $input['difficulty'] ?? null;
            $engagement = $input['engagement'] ?? null;
            $completion = $input['completion'] ?? null;
            $favoritePart = $input['favoritePart'] ?? null;
            $challengingPart = $input['challengingPart'] ?? null;
            $suggestions = $input['suggestions'] ?? null;
            $wouldRecommend = $input['wouldRecommend'] ?? null;
            
            if (!$token || !$parentName || !$parentEmail) {
                http_response_code(400);
                echo json_encode(['error' => 'token, parentName, and parentEmail are required']);
                exit;
            }
            
            $result = $api->submitFeedback($token, $parentName, $parentEmail, $difficulty, $engagement, $completion, $favoritePart, $challengingPart, $suggestions, $wouldRecommend);
            echo json_encode($result);
            break;
            
        case 'feedback_get':
            require_once __DIR__ . '/FeedbackAPI.php';
            $api = new FeedbackAPI();
            
            $worksheetId = $_GET['worksheet_id'] ?? null;
            $user = $_GET['user'] ?? null;
            
            if (!$worksheetId || !$user) {
                http_response_code(400);
                echo json_encode(['error' => 'worksheet_id and user are required']);
                exit;
            }
            
            $result = $api->getFeedback($worksheetId, $user);
            echo json_encode($result);
            break;
            
        case 'email_send':
            require_once __DIR__ . '/EmailAPI.php';
            $api = new EmailAPI();
            
            $childId = $input['child_id'] ?? null;
            $worksheetId = $input['worksheet_id'] ?? null;
            $parentEmail = $input['parent_email'] ?? null;
            $emailType = $input['email_type'] ?? 'both';
            
            if (!$childId || !$worksheetId || !$parentEmail) {
                http_response_code(400);
                echo json_encode(['error' => 'child_id, worksheet_id, and parent_email are required']);
                exit;
            }
            
            switch ($emailType) {
                case 'feedback':
                    $result = $api->sendFeedbackEmail($childId, $worksheetId, $parentEmail);
                    break;
                case 'worksheet':
                    $result = $api->sendWorksheetEmail($childId, $worksheetId, $parentEmail);
                    break;
                case 'both':
                default:
                    $result = $api->sendBothEmails($childId, $worksheetId, $parentEmail);
                    break;
            }
            
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