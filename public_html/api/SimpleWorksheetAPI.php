<?php
require_once 'conf.php';
require_once 'OpenaiProvider.php';
require_once 'UserAuthAPI.php';
require_once 'env.php';
loadEnv();

class SimpleWorksheetAPI {
    private $pdo;
    private $openai;
    private $authAPI;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
        $this->authAPI = new UserAuthAPI();
        
        // Initialize OpenAI
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if ($apiKey) {
            $this->openai = new OpenaiProvider($apiKey);
        }
    }
    
    /**
     * Get authenticated user from Authorization header
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
     * Generate new OpenAI content for a child and save to database
     * POST /api/SimpleWorksheetAPI.php?action=generate-content
     * Body: { "child_id": "child_123", "date": "2025-01-15" }
     */
    public function generateContent($childId, $date = null) {
        try {
            $user = $this->getAuthenticatedUser();
            
            if (!$this->openai) {
                throw new Exception('OpenAI API not configured');
            }
            
            $date = $date ?? date('Y-m-d');
            
            // Get child information (verify ownership)
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.plan 
                FROM children c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $user['id']]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$child) {
                throw new Exception('Child not found');
            }
            
            // Check if content already exists
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE child_id = ? AND date = ?");
            $stmt->execute([$childId, $date]);
            if ($stmt->fetch()) {
                throw new Exception('Worksheet already exists for this date');
            }
            
            // Generate content using OpenAI
            $content = $this->generateWorksheetContent($child, $date);
            
            // Save to database
            $worksheetId = Database::generateWorksheetId();
            $stmt = $this->pdo->prepare("
                INSERT INTO worksheets (id, child_id, date, content, pdf_path) 
                VALUES (?, ?, ?, ?, '')
            ");
            $stmt->execute([$worksheetId, $childId, $date, $content]);
            
            return [
                'status' => 'success',
                'worksheet_id' => $worksheetId,
                'content' => $content,
                'message' => 'Content generated and saved successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Create PDF from saved content and save to server
     * POST /api/SimpleWorksheetAPI.php?action=create-pdf
     * Body: { "worksheet_id": "ws_123" }
     */
    public function createPDF($worksheetId) {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Get worksheet content (verify ownership)
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name 
                FROM worksheets w 
                JOIN children c ON w.child_id = c.id 
                WHERE w.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$worksheetId, $user['id']]);
            $worksheet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$worksheet) {
                throw new Exception('Worksheet not found');
            }
            
            // Create PDF directory in public_html/worksheets
            $pdfDir = __DIR__ . '/../worksheets/' . $worksheet['child_id'];
            if (!file_exists($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            
            // Generate PDF filename
            $pdfFilename = $worksheet['date'] . '.pdf';
            $pdfPath = $pdfDir . '/' . $pdfFilename;
            
            // Generate PDF using MPDF
            $this->generatePDFFile($worksheet['content'], $worksheet['child_name'], $worksheet['date'], $pdfPath);
            
            // Verify PDF was created
            if (!file_exists($pdfPath)) {
                throw new Exception('PDF file was not created successfully');
            }
            
            // Update database with PDF path
            $relativePath = 'worksheets/' . $worksheet['child_id'] . '/' . $pdfFilename;
            $stmt = $this->pdo->prepare("UPDATE worksheets SET pdf_path = ? WHERE id = ?");
            $stmt->execute([$relativePath, $worksheetId]);
            
            // Generate download URL using new routing system
            $downloadUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                           '://' . $_SERVER['HTTP_HOST'] . '/api/worksheets/pdf?worksheet_id=' . $worksheetId;
            
            return [
                'status' => 'success',
                'pdf_path' => $relativePath,
                'download_url' => $downloadUrl,
                'message' => 'PDF created successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Download PDF file (public endpoint - no auth required)
     * GET /api/SimpleWorksheetAPI.php?worksheet_id=ws_123
     */
    public function downloadPDF($worksheetId) {
        try {
            // Get worksheet info
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name 
                FROM worksheets w 
                JOIN children c ON w.child_id = c.id 
                WHERE w.id = ?
            ");
            $stmt->execute([$worksheetId]);
            $worksheet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$worksheet) {
                throw new Exception('Worksheet not found');
            }
            
            $pdfPath = __DIR__ . '/../' . $worksheet['pdf_path'];
            
            if (!file_exists($pdfPath)) {
                throw new Exception('PDF file not found at: ' . $pdfPath . ' (pdf_path from DB: ' . $worksheet['pdf_path'] . ')');
            }
            
            // Stream PDF to browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $worksheet['child_name'] . '_Worksheet_' . $worksheet['date'] . '.pdf"');
            header('Content-Length: ' . filesize($pdfPath));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($pdfPath);
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Get user's worksheets
     * GET /api/SimpleWorksheetAPI.php?action=list
     */
    public function getWorksheets() {
        try {
            $user = $this->getAuthenticatedUser();
            
            $stmt = $this->pdo->prepare("
                SELECT w.id, w.date, w.content, w.pdf_path, c.name as child_name, c.id as child_id
                FROM worksheets w 
                JOIN children c ON w.child_id = c.id 
                WHERE c.user_id = ? 
                ORDER BY w.date DESC
            ");
            $stmt->execute([$user['id']]);
            $worksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'worksheets' => $worksheets
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    // Private helper methods
    private function generateWorksheetContent($child, $date) {
        $prompt = $this->buildWorksheetPrompt($child['name'], $child['age_group'], [$child['interest1'], $child['interest2']], $date);
        $systemPrompt = $this->getSystemPrompt();
        
        $result = $this->openai->callApiWithoutEcho($prompt, $systemPrompt);
        
        if (!$result || !isset($result['content'])) {
            throw new Exception('Failed to generate worksheet content');
        }
        
        return $result['content'];
    }
    
    private function buildWorksheetPrompt($childName, $ageGroup, $interests, $date) {
        $interestText = implode(' and ', array_filter($interests));
        
        return "Create worksheet content for {$childName}, age {$ageGroup}, who loves {$interestText}.

        Return ONLY HTML content (no DOCTYPE, no <html>, no <head>, no <style> tags).

        Format:
        <h3>Math Problems</h3>
        <ol>
        <li>Question 1</li>
        <li>Question 2</li>
        ...10 questions total
        </ol>

        <h3>English Questions</h3>
        <ol>
        <li>Question 1</li>
        <li>Question 2</li>
        ...10 questions total
        </ol>

        Requirements:
        - EXACTLY 10 math questions, EXACTLY 10 English questions
        - Each question 1-2 lines maximum
        - Use {$interestText} themes in questions
        - Age-appropriate for {$ageGroup} year olds";
    }
    
    private function getSystemPrompt() {
        return "You are an educational content creator. Follow instructions exactly.

        CRITICAL: Return ONLY HTML body content. NO DOCTYPE, NO <html>, NO <head>, NO <style> tags.
        
        Use only: <h3>, <ol>, <li>, <p> tags.
        
        Generate exactly what is requested - no additional content or explanations.";
    }
    
    private function generatePDFFile($htmlContent, $childName, $date, $outputPath) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        // Create temp directory for MPDF with full permissions
        $tempDir = sys_get_temp_dir() . '/mpdf_worksheets';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        // Create MPDF instance with system temp directory
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'tempDir' => $tempDir
        ]);
        
        // Set document properties
        $mpdf->SetTitle($childName . "'s Worksheet - " . date('F j, Y', strtotime($date)));
        $mpdf->SetAuthor('Yes Homework');
        $mpdf->SetCreator('Yes Homework');
        
        // Add CSS styling for 2-page layout
        $css = '
            body { 
                font-family: Arial, sans-serif; 
                font-size: 13px; 
                line-height: 1.8; 
                margin: 0; 
                padding: 25px; 
                color: #333;
            }
            .page-header { 
                color: #2563eb; 
                text-align: center; 
                font-size: 22px; 
                margin-bottom: 15px;
                border-bottom: 3px solid #2563eb;
                padding-bottom: 15px;
            }
            .page-subtitle { 
                color: #64748b; 
                text-align: center; 
                font-size: 16px; 
                margin-bottom: 40px;
                font-weight: normal;
            }
            .section-title { 
                color: #1e40af; 
                font-size: 20px; 
                margin-bottom: 30px;
                border-left: 5px solid #3b82f6;
                padding-left: 20px;
                font-weight: bold;
                text-align: center;
                background-color: #f8fafc;
                padding: 15px;
                border-radius: 5px;
            }
            .question-item { 
                margin-bottom: 45px;
                padding: 15px;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background-color: #fafbfc;
            }
            .question-text {
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 20px;
                font-size: 14px;
            }
            .answer-space {
                border-bottom: 2px dotted #94a3b8;
                height: 40px;
                margin-bottom: 10px;
            }
            .page-break {
                page-break-before: always;
            }
            .page-1 {
                min-height: 700px;
            }
            .page-2 {
                min-height: 700px;
            }
        ';
        
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        
        // Debug: Log the raw content from AI
        error_log("Raw AI content: " . $htmlContent);
        
        // Clean up the HTML content by removing inline CSS and fixing structure
        $cleanContent = $this->cleanHtmlContent($htmlContent);
        
        // Debug: Log the cleaned content
        error_log("Cleaned content: " . $cleanContent);
        
        // Parse content into sections
        $sections = $this->parseWorksheetSections($cleanContent);
        
        // Build full HTML content with page breaks
        $fullHtml = $this->buildTwoPageWorksheet($childName, $date, $sections);
        
        // Write content and save to file
        $mpdf->WriteHTML($fullHtml, \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output($outputPath, 'F');
    }
    
    private function cleanHtmlContent($htmlContent) {
        $content = $htmlContent;
        
        // If AI returned full HTML document, extract only body content
        if (strpos($content, '<!DOCTYPE') !== false || strpos($content, '<html>') !== false) {
            // Extract content between <body> tags
            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $content, $matches)) {
                $content = $matches[1];
            } else {
                // Fallback: remove DOCTYPE, html, head tags
                $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
                $content = preg_replace('/<html[^>]*>/i', '', $content);
                $content = preg_replace('/<\/html>/i', '', $content);
                $content = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $content);
                $content = preg_replace('/<body[^>]*>/i', '', $content);
                $content = preg_replace('/<\/body>/i', '', $content);
            }
        }
        
        // Remove CSS style blocks
        $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
        
        // Remove inline style attributes
        $content = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $content);
        
        // Remove class attributes since we're not using them
        $content = preg_replace('/\s*class\s*=\s*["\'][^"\']*["\']/i', '', $content);
        
        // Clean up extra whitespace and newlines
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/>\s+</', '><', $content);
        $content = trim($content);
        
        return $content;
    }
    
    private function parseWorksheetSections($content) {
        $sections = ['math' => [], 'english' => []];
        
        // Extract math questions
        if (preg_match('/<h3[^>]*>Math Problems<\/h3>\s*<ol>(.*?)<\/ol>/is', $content, $matches)) {
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $matches[1], $mathMatches);
            $sections['math'] = $mathMatches[1];
        }
        
        // Extract English questions
        if (preg_match('/<h3[^>]*>English Questions<\/h3>\s*<ol>(.*?)<\/ol>/is', $content, $matches)) {
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $matches[1], $englishMatches);
            $sections['english'] = $englishMatches[1];
        }
        
        return $sections;
    }
    
    private function buildTwoPageWorksheet($childName, $date, $sections) {
        $html = '';
        
        // PAGE 1 - Math Questions
        $html .= '<div class="page-1">';
        $html .= '<div class="page-header">' . htmlspecialchars($childName) . '\'s Math Worksheet</div>';
        $html .= '<div class="page-subtitle">' . date('F j, Y', strtotime($date)) . '</div>';
        $html .= '<div class="section-title">Math Problems</div>';
        
        foreach ($sections['math'] as $index => $question) {
            $questionNum = $index + 1;
            $html .= '<div class="question-item">';
            $html .= '<div class="question-text">' . $questionNum . '. ' . strip_tags($question) . '</div>';
            $html .= '<div class="answer-space"></div>';
            $html .= '<div class="answer-space"></div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // PAGE 2 - English Questions
        $html .= '<div class="page-break page-2">';
        $html .= '<div class="page-header">' . htmlspecialchars($childName) . '\'s English Worksheet</div>';
        $html .= '<div class="page-subtitle">' . date('F j, Y', strtotime($date)) . '</div>';
        $html .= '<div class="section-title">English Questions</div>';
        
        foreach ($sections['english'] as $index => $question) {
            $questionNum = $index + 1;
            $html .= '<div class="question-item">';
            $html .= '<div class="question-text">' . $questionNum . '. ' . strip_tags($question) . '</div>';
            $html .= '<div class="answer-space"></div>';
            $html .= '<div class="answer-space"></div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
}

// Handle direct API calls
if (basename($_SERVER['SCRIPT_NAME']) === 'SimpleWorksheetAPI.php') {
    $api = new SimpleWorksheetAPI();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'generate-content':
                    $childId = $input['child_id'] ?? null;
                    $date = $input['date'] ?? null;
                    
                    if (!$childId) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'child_id is required']);
                        exit;
                    }
                    
                    $result = $api->generateContent($childId, $date);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'create-pdf':
                    $worksheetId = $input['worksheet_id'] ?? null;
                    
                    if (!$worksheetId) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'worksheet_id is required']);
                        exit;
                    }
                    
                    $result = $api->createPDF($worksheetId);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Action parameter required']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'list') {
            $result = $api->getWorksheets();
            header('Content-Type: application/json');
            echo json_encode($result);
        } elseif (isset($_GET['worksheet_id'])) {
            $api->downloadPDF($_GET['worksheet_id']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'worksheet_id parameter required']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
} 