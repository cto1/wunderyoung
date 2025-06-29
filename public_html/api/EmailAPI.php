<?php
require_once 'conf.php';
require_once 'UserAuthAPI.php';
require_once 'env.php';
loadEnv();

class EmailAPI {
    private $pdo;
    private $authAPI;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
        $this->authAPI = new UserAuthAPI();
    }
    
    /**
     * Get user from Authorization header
     */
    private function getAuthenticatedUser() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new Exception('Authorization header required');
        }
        
        $token = substr($authHeader, 7);
        $user = $this->authAPI->getUserFromToken($token);
        
        if (!$user) {
            throw new Exception('Invalid or expired token');
        }
        
        return $user;
    }
    
    /**
     * Send feedback email to parent
     * POST /api/EmailAPI.php?action=send-feedback
     */
    public function sendFeedbackEmail($childId, $worksheetId, $parentEmail) {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Get child and worksheet info
            $stmt = $this->pdo->prepare("
                SELECT c.name as child_name, w.date, w.content 
                FROM children c 
                JOIN worksheets w ON c.id = w.child_id 
                WHERE c.id = ? AND w.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $worksheetId, $user['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                throw new Exception('Worksheet not found');
            }
            
            // Generate feedback token
            $feedbackToken = $this->generateFeedbackToken($worksheetId);
            
            // Create feedback URL
            $feedbackUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                           '://' . $_SERVER['HTTP_HOST'] . '/feedback.php?token=' . $feedbackToken;
            
            // Send email
            $subject = $data['child_name'] . "'s Worksheet Feedback - " . date('F j, Y', strtotime($data['date']));
            $message = $this->buildFeedbackEmail($data['child_name'], $data['date'], $feedbackUrl);
            
            $this->sendEmail($parentEmail, $subject, $message);
            
            return [
                'status' => 'success',
                'feedback_url' => $feedbackUrl,
                'message' => 'Feedback email sent successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send new worksheet email to parent
     * POST /api/EmailAPI.php?action=send-worksheet
     */
    public function sendWorksheetEmail($childId, $worksheetId, $parentEmail) {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Get child and worksheet info
            $stmt = $this->pdo->prepare("
                SELECT c.name as child_name, w.date, w.pdf_path 
                FROM children c 
                JOIN worksheets w ON c.id = w.child_id 
                WHERE c.id = ? AND w.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $worksheetId, $user['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                throw new Exception('Worksheet not found');
            }
            
            if (empty($data['pdf_path'])) {
                throw new Exception('PDF not generated yet');
            }
            
            // Create download URL
            $downloadUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                           '://' . $_SERVER['HTTP_HOST'] . '/api/SimpleWorksheetAPI.php?worksheet_id=' . $worksheetId;
            
            // Send email
            $subject = $data['child_name'] . "'s New Worksheet - " . date('F j, Y', strtotime($data['date']));
            $message = $this->buildWorksheetEmail($data['child_name'], $data['date'], $downloadUrl);
            
            $this->sendEmail($parentEmail, $subject, $message);
            
            return [
                'status' => 'success',
                'download_url' => $downloadUrl,
                'message' => 'Worksheet email sent successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Send both feedback and worksheet emails
     * POST /api/EmailAPI.php?action=send-both
     */
    public function sendBothEmails($childId, $worksheetId, $parentEmail) {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Get child and worksheet info
            $stmt = $this->pdo->prepare("
                SELECT c.name as child_name, w.date, w.content, w.pdf_path 
                FROM children c 
                JOIN worksheets w ON c.id = w.child_id 
                WHERE c.id = ? AND w.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $worksheetId, $user['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                throw new Exception('Worksheet not found');
            }
            
            // Generate feedback token
            $feedbackToken = $this->generateFeedbackToken($worksheetId);
            $feedbackUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                           '://' . $_SERVER['HTTP_HOST'] . '/feedback.php?token=' . $feedbackToken;
            
            // Create download URL
            $downloadUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                           '://' . $_SERVER['HTTP_HOST'] . '/api/SimpleWorksheetAPI.php?worksheet_id=' . $worksheetId;
            
            // Send combined email
            $subject = $data['child_name'] . "'s Worksheet & Feedback - " . date('F j, Y', strtotime($data['date']));
            $message = $this->buildCombinedEmail($data['child_name'], $data['date'], $downloadUrl, $feedbackUrl);
            
            $this->sendEmail($parentEmail, $subject, $message);
            
            return [
                'status' => 'success',
                'download_url' => $downloadUrl,
                'feedback_url' => $feedbackUrl,
                'message' => 'Emails sent successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function generateFeedbackToken($worksheetId) {
        return 'fb_' . bin2hex(random_bytes(16)) . '_' . $worksheetId;
    }
    
    private function sendEmail($to, $subject, $message) {
        // Use Mailgun API to send emails
        $apiKey = $_ENV['MAILGUN_API_KEY'] ?? null;
        $domain = $_ENV['MAILGUN_DOMAIN'] ?? null;
        $fromName = $_ENV['MAILGUN_FROM_NAME'] ?? 'Yes Homework';
        $fromEmail = $_ENV['MAILGUN_FROM_EMAIL'] ?? 'support@yeshomework.com';
        
        if (!$apiKey || !$domain) {
            throw new Exception('Mailgun configuration not found');
        }
        
        $url = "https://api.mailgun.net/v3/{$domain}/messages";
        $from = "{$fromName} <{$fromEmail}>";
        
        $data = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $message
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "api:{$apiKey}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Failed to send email via Mailgun. HTTP Code: ' . $httpCode . ', Response: ' . $response);
        }
        
        $result = json_decode($response, true);
        if (!$result || !isset($result['id'])) {
            throw new Exception('Invalid response from Mailgun: ' . $response);
        }
    }
    
    private function buildFeedbackEmail($childName, $date, $feedbackUrl) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #3b82f6;'>📝 {$childName}'s Worksheet Feedback</h2>
                <p>Hello!</p>
                <p>We've created a personalized worksheet for <strong>{$childName}</strong> for <strong>" . date('F j, Y', strtotime($date)) . "</strong>.</p>
                <p>Please take a moment to provide feedback on how the worksheet worked for your child. Your input helps us create better content!</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$feedbackUrl}' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        📋 Leave Feedback
                    </a>
                </div>
                
                <p>Thank you for using Yes Homework!</p>
                <p>Best regards,<br>The Yes Homework Team</p>
            </div>
        </body>
        </html>";
    }
    
    private function buildWorksheetEmail($childName, $date, $downloadUrl) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #3b82f6;'>📚 {$childName}'s New Worksheet</h2>
                <p>Hello!</p>
                <p>We've created a personalized worksheet for <strong>{$childName}</strong> for <strong>" . date('F j, Y', strtotime($date)) . "</strong>.</p>
                <p>Click the button below to download and print the worksheet:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$downloadUrl}' style='background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        📥 Download Worksheet
                    </a>
                </div>
                
                <p>Enjoy learning with Yes Homework!</p>
                <p>Best regards,<br>The Yes Homework Team</p>
            </div>
        </body>
        </html>";
    }
    
    private function buildCombinedEmail($childName, $date, $downloadUrl, $feedbackUrl) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #3b82f6;'>📚 {$childName}'s Worksheet & Feedback</h2>
                <p>Hello!</p>
                <p>We've created a personalized worksheet for <strong>{$childName}</strong> for <strong>" . date('F j, Y', strtotime($date)) . "</strong>.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$downloadUrl}' style='background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;'>
                        📥 Download Worksheet
                    </a>
                    <a href='{$feedbackUrl}' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        📋 Leave Feedback
                    </a>
                </div>
                
                <p>Thank you for using Yes Homework!</p>
                <p>Best regards,<br>The Yes Homework Team</p>
            </div>
        </body>
        </html>";
    }
}

// Handle direct API calls
if (basename($_SERVER['SCRIPT_NAME']) === 'EmailAPI.php') {
    $api = new EmailAPI();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action'])) {
            $childId = $input['child_id'] ?? null;
            $worksheetId = $input['worksheet_id'] ?? null;
            $parentEmail = $input['parent_email'] ?? null;
            
            if (!$childId || !$worksheetId || !$parentEmail) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'child_id, worksheet_id, and parent_email are required']);
                exit;
            }
            
            switch ($_GET['action']) {
                case 'send-feedback':
                    $result = $api->sendFeedbackEmail($childId, $worksheetId, $parentEmail);
                    break;
                    
                case 'send-worksheet':
                    $result = $api->sendWorksheetEmail($childId, $worksheetId, $parentEmail);
                    break;
                    
                case 'send-both':
                    $result = $api->sendBothEmails($childId, $worksheetId, $parentEmail);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                    exit;
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
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