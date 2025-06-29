<?php
// Disable displaying errors to output (important for JSON responses)
ini_set('display_errors', 0);
// But still log errors to the error log
error_reporting(E_ALL);

// Wrap the entire script in a try-catch to ensure proper JSON responses
try {

// --- Allowed Origins ---
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Detect Environment and Set Base URL ---
$host = $_SERVER['HTTP_HOST'] ?? '';
if (strpos($host, 'demo.exactsum.com') !== false) {
    $base_url = "https://demo.exactsum.com/api";
} else {
    $base_url = "https://exactsum.com/api";
}

// --- Extract JWT Token from Request Headers ---
$headers = getallheaders();
$jwt_token = $headers['Authorization'] ?? null;

// if (!$jwt_token) {
//     http_response_code(401);
//     echo json_encode(["status" => "error", "message" => "Missing JWT token in request headers."]);
//     exit();
// }

// --- API Endpoints Mapping ---
$apiEndpoints = [

    // ----- [Login Flow] -----
    "request_login" => "$base_url/auth/login",
    "verify_login" => "$base_url/auth/verify?email={email}&token={token}",
    "JWT_token" => "$base_url/auth/token",

    
    // ----- [Organization] -----
    "get_organization" => "$base_url/organizations/{org_id}",
    "update_organization" => "$base_url/organizations/{org_id}",


    // ----- [Users] -----
    "signup" => "$base_url/auth/signup",


    // ----- [Teams] -----
    "GET_org_users" => "$base_url/auth/{org_id}/users",
    "POST_invite_user" => "$base_url/organizations/{org_id}/users",
    "DEL_user" => "$base_url/organizations/{org_id}/users/{user_id}",
    "PUT_change_user_type" => "$base_url/organizations/{org_id}/users/{user_id}/role",

    
    // ----- [Settings] -----
    "get_settings" => "$base_url/organizations/{org_id}/settings",
    "update_setting" => "$base_url/organizations/{org_id}/settings",
    "reset_to_defaults" => "$base_url/organizations/{org_id}/settings/reset",


    // ----- [Vault Project] -----
    
    // [Vault Project -> Project Operation]
    "GET_vault_projects" => "$base_url/organizations/{org_id}/vault/projects?include_file_counts=true",
    "GET_vault_project" => "$base_url/organizations/{org_id}/vault/projects/{project_id}",
    "POST_vault_project" => "$base_url/organizations/{org_id}/vault/projects",
    "PUT_vault_project" => "$base_url/organizations/{org_id}/vault/projects/{project_id}",
    "DELETE_vault_project" => "$base_url/organizations/{org_id}/vault/projects/{project_id}",
    
    // [Vault Project -> File Operation -> File CRUD]
    'GET_BS_FILES' => "$base_url/organizations/{org_id}/vault/projects/{project_id}/files",
    'GET_BS_FILE' => "$base_url/organizations/{org_id}/vault/projects/{project_id}/files/{file_id}",
    "POST_BS_FILES" => "$base_url/organizations/{org_id}/vault/projects/{project_id}/files",
    'PUT_BS_FILE' => "$base_url/organizations/{org_id}/vault/projects/{project_id}/files/{file_id}",
    'DEL_BS_FILE' => "$base_url/organizations/{org_id}/vault/projects/{project_id}/files/{file_id}",
    // [Vault Project -> File Operation -> OCR Operations]
    'POST_process_OCR' => "$base_url/vault/files/{file_id}/ocr",
    'GET_OCR_status' => "$base_url/vault/files/{file_id}/ocr-status",
    'GET_project_transactions' => "$base_url/vault/projects/{project_id}/transactions",
    // [Vault Project -> File Operation -> Citations]
    // 'GET_file_citation_mappings' => "$base_url/vault/files/{file_id}/transaction-citations",

    // [Vault Project -> Bank Statement Analysis -> AI]
    'POST_BS_section_DP_AI' => "$base_url/organizations/{org_id}/bank-statements/{file_id}/analyze-section",
    // [Vault Project -> Bank Statement Analysis -> SQL]
    'POST_BS_section_SQL' => "$base_url/organizations/{org_id}/bank-statements/analyze-section-sql",
    // [Vault Project -> Bank Statement Analysis -> Helpers]
    

    // xx
    'POST_BSA_from_OCR_File' => "$base_url/organizations/{org_id}/bank-statements/{file_id}/analyze",

    
    // ----- [Companies House] -----

    'GET_officers_from_CH' => "$base_url/company/{company_number}/officers",
    'GET_company' => "$base_url/organizations/{org_id}/projects/{project_id}/companies/{company_number}",
    'GET_companies' => "$base_url/organizations/{org_id}/projects/{project_id}/companies",
    'POST_company' => "$base_url/organizations/{org_id}/projects/{project_id}/companies/{company_number}",
    'DELETE_company' => "$base_url/organizations/{org_id}/projects/{project_id}/companies/{company_number}",

    // ----- [Usage] -----
    "getUsageData" => "$base_url/usage/organization/{org_id}",
    "getUsageDataByFile" => "$base_url/usage/file/{file_id}",

    // ----- [Ideas & Bugs] -----
    "POST_submitIdea" => "$base_url/ideas/organization/{org_id}",
    "GET_getIdeas" => "$base_url/ideas/organization/{org_id}", 
    "POST_voteIdea" => "$base_url/ideas/{idea_id}/vote",
    
    // ----- [Yes Homework - Download System] -----
    "get_token_info" => "/api/DownloadTokenAPI.php?action=get_info&token={token}",
    "create_download_token" => "/api/DownloadTokenAPI.php",
    "submit_feedback" => "/api/FeedbackAPI.php",
    "download_pdf" => "/api/DownloadAPI.php?token={token}",
];


// --- Extract Query Parameters ---
$apiKey = $_GET['api'] ?? null;
$orgId = $_GET['org_id'] ?? null;
$token = $_GET['token'] ?? null;
$userId = $_GET['user_id'] ?? null;
$projectId = $_GET['project_id'] ?? null;
$fileId = $_GET['file_id'] ?? null;
$companyNumber = $_GET['company_number'] ?? null;
$ideaId = $_GET['idea_id'] ?? null;
$filter = $_GET['filter'] ?? null; 


if (!$apiKey || !isset($apiEndpoints[$apiKey])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or missing API key."]);
    exit();
}

// Validate Required Parameters
if (strpos($apiEndpoints[$apiKey], '{org_id}') !== false && !$orgId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing org_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{token}') !== false && !$token) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing token parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{user_id}') !== false && !$userId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing user_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{project_id}') !== false && !$projectId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing project_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{file_id}') !== false && !$fileId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing file_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{company_number}') !== false && !$companyNumber) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing company_number parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{idea_id}') !== false && !$ideaId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing idea_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{filter}') !== false && !$filter) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing filter parameter."]);
    exit();
}

// Construct API URL
$apiUrl = str_replace(
    ['{org_id}', '{token}', '{user_id}', '{project_id}', '{file_id}', '{company_number}', '{idea_id}', '{filter}'],
    [$orgId, $token, $userId, $projectId, $fileId, $companyNumber, $ideaId, $filter],
    $apiEndpoints[$apiKey]
);

// Append any other GET parameters (like for filtering or pagination)
$otherParams = array_diff_key($_GET, array_flip(['api', 'org_id', 'token', 'user_id', 'project_id', 'file_id', 'company_number', 'idea_id', 'filter']));
if (!empty($otherParams)) {
    $apiUrl .= (strpos($apiUrl, '?') === false ? '?' : '&') . http_build_query($otherParams);
}

// --- Handle Yes Homework Local API Calls ---
$yesHomeworkApis = ['get_token_info', 'create_download_token', 'submit_feedback', 'download_pdf'];
if (in_array($apiKey, $yesHomeworkApis)) {
    // For local APIs, prepend the document root
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $localApiPath = $documentRoot . $apiUrl;
    
    // Include and execute the local API
    if (file_exists($localApiPath)) {
        // Temporarily change working directory and include the API
        $oldCwd = getcwd();
        chdir(dirname($localApiPath));
        
        // Capture output
        ob_start();
        include $localApiPath;
        $response = ob_get_contents();
        ob_end_clean();
        
        // Restore working directory
        chdir($oldCwd);
        
        // Output the response
        header("Content-Type: application/json");
        echo $response;
        exit();
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Local API file not found: $localApiPath"]);
        exit();
    }
}

// --- Handle Vault File Upload Request [POST_BS_FILES] ---
if ($apiKey === 'POST_BS_FILES') {
    if (!isset($_FILES['files'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "No files were uploaded."]);
        exit();
    }

    $orgId = $_POST['org_id'] ?? null;
    $projectId = $_POST['project_id'] ?? null;

    if (!$orgId || !$projectId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing org_id or project_id."]);
        exit();
    }

    $uploadUrl = "$base_url/organizations/$orgId/vault/projects/$projectId/files";

    $multiCurl = [];
    $results = [];

    foreach ($_FILES['files']['tmp_name'] as $i => $tmpName) {
        $postData = [
            'file' => new CURLFile($tmpName, $_FILES['files']['type'][$i], $_FILES['files']['name'][$i])
        ];

        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $jwt_token"
        ]);

        $multiCurl[] = $ch;
    }

    $mh = curl_multi_init();
    foreach ($multiCurl as $ch) {
        curl_multi_add_handle($mh, $ch);
    }

    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    foreach ($multiCurl as $ch) {
        $response = curl_multi_getcontent($ch);
        $results[] = json_decode($response, true);
        curl_multi_remove_handle($mh, $ch);
    }

    curl_multi_close($mh);

    header("Content-Type: application/json");
    echo json_encode([
        "status" => "success",
        "uploaded" => count($results),
        "results" => $results
    ]);
    exit();
}

// --- Handle Other API Requests [OTHER] ---
else {
    $inputData = file_get_contents("php://input");
    $method = $_SERVER['REQUEST_METHOD'];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: $jwt_token"
    ]);

    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
            break;
        case 'GET':
            // No extra setup needed for GET
            break;
        default:
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Method not allowed."]);
            exit();
    }

    // Execute API Request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "cURL Error: " . curl_error($ch)]);
        curl_close($ch);
        exit();
    }

    curl_close($ch);

    // Forward API Response
    header("Content-Type: application/json");
    http_response_code($httpCode);
    echo $response;
}

} catch (Exception $e) {
    // Log the error server-side
    error_log("Proxy server exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Return a proper JSON error response
    header("Content-Type: application/json");
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error occurred: " . $e->getMessage()
    ]);
}
?>
