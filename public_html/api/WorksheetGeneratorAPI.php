<?php

require_once 'conf.php';
require_once 'OpenaiProvider.php';
require_once 'WorksheetAPI.php';
require_once 'FeedbackAPI.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class WorksheetGeneratorAPI {
    private $db;
    private $pdo;
    private $openai;
    private $worksheetAPI;
    private $feedbackAPI;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
        $this->worksheetAPI = new WorksheetAPI();
        $this->feedbackAPI = new FeedbackAPI();
        
        // Initialize OpenAI
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if ($apiKey) {
            $this->openai = new OpenaiProvider($apiKey, 'gpt-4');
        }
    }

    // Generate a worksheet for a child
    public function generateWorksheet($userId, $childId, $date = null) {
        try {
            if (!$this->openai) {
                throw new Exception('OpenAI API not configured');
            }

            $date = $date ?? date('Y-m-d');

            // Get child information
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.plan 
                FROM children c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $userId]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$child) {
                throw new Exception('Child not found or unauthorized');
            }

            // Check if worksheet already exists
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE child_id = ? AND date = ?");
            $stmt->execute([$childId, $date]);
            if ($stmt->fetch()) {
                throw new Exception('Worksheet already exists for this date');
            }

            // Generate worksheet content
            $content = $this->generateWorksheetContent($child, $date);

            // Save worksheet to database (no PDF path, content only)
            $worksheetData = [
                'child_id' => $childId,
                'date' => $date,
                'content' => $content
            ];

            $result = $this->worksheetAPI->createWorksheet($worksheetData);

            if ($result['status'] === 'success') {
                return [
                    'status' => 'success',
                    'worksheet_id' => $result['worksheet_id'],
                    'content' => $content,
                    'message' => 'Worksheet generated successfully'
                ];
            }

            return $result;

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Generate and download PDF on-demand (streams to browser)
    public function downloadWorksheetPDF($worksheetId, $token = null) {
        try {
            // Get worksheet and child information
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name, c.age_group
                FROM worksheets w 
                JOIN children c ON w.child_id = c.id 
                WHERE w.id = ?
            ");
            $stmt->execute([$worksheetId]);
            $worksheet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$worksheet) {
                throw new Exception('Worksheet not found');
            }

            // TODO: Validate download token here when implementing token system

            // Generate PDF and stream to browser
            $this->streamPDFToBrowser($worksheet['content'], $worksheet['child_name'], $worksheet['date']);

        } catch (Exception $e) {
            // Return error as JSON if PDF generation fails
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Stream PDF directly to browser for download
    private function streamPDFToBrowser($htmlContent, $childName, $date) {
        // Add answer spacing to the content
        $pdfContent = $this->addAnswerSpacing($htmlContent);
        
        // Try TCPDF first, fallback to DOMPDF
        if (class_exists('TCPDF')) {
            $this->streamPDFWithTCPDF($pdfContent, $childName, $date);
        } elseif (class_exists('Dompdf\Dompdf')) {
            $this->streamPDFWithDOMPDF($pdfContent, $childName, $date);
        } else {
            throw new Exception('No PDF library available');
        }
    }

    // Add spacing for answers in the HTML content
    private function addAnswerSpacing($htmlContent) {
        // Add answer lines after questions or problems
        $patterns = [
            // Add lines after math problems
            '/(<p[^>]*>.*?\d+\s*[\+\-\×\÷]\s*\d+\s*=\s*<\/p>)/i' => '$1<div style="border-bottom: 1px solid #ccc; margin: 10px 0; height: 20px;"></div>',
            
            // Add lines after questions ending with ?
            '/(<p[^>]*>.*?\?<\/p>)/i' => '$1<div style="border-bottom: 1px solid #ccc; margin: 10px 0; height: 20px;"></div>',
            
            // Add space after "Answer:" or "Solution:"
            '/(<p[^>]*>.*?(?:Answer|Solution):\s*<\/p>)/i' => '$1<div style="border-bottom: 1px solid #ccc; margin: 10px 0; height: 20px;"></div>',
            
            // Add lines for fill-in-the-blank (_______ patterns)
            '/_____+/' => '<span style="border-bottom: 1px solid #000; display: inline-block; min-width: 80px; margin: 0 5px;">&nbsp;</span>'
        ];

        foreach ($patterns as $pattern => $replacement) {
            $htmlContent = preg_replace($pattern, $replacement, $htmlContent);
        }

        return $htmlContent;
    }

    // Stream PDF using TCPDF
    private function streamPDFWithTCPDF($htmlContent, $childName, $date) {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Yes Homework');
        $pdf->SetAuthor('Yes Homework');
        $pdf->SetTitle($childName . "'s Worksheet - " . date('F j, Y', strtotime($date)));

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Add a page
        $pdf->AddPage();

        // Add title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $childName . "'s Worksheet", 0, 1, 'C');
        $pdf->Cell(0, 10, date('F j, Y', strtotime($date)), 0, 1, 'C');
        $pdf->Ln(10);

        // Convert HTML to PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->writeHTML($htmlContent, true, false, true, false, '');

        // Output PDF to browser
        $filename = $childName . '_Worksheet_' . $date . '.pdf';
        $pdf->Output($filename, 'D'); // 'D' = force download
    }

    // Stream PDF using DOMPDF
    private function streamPDFWithDOMPDF($htmlContent, $childName, $date) {
        $fullHtml = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #3b82f6; text-align: center; }
        .worksheet-section { margin-bottom: 30px; }
        @page { margin: 20mm; }
    </style>
</head>
<body>
    <h1>{$childName}'s Worksheet</h1>
    <h2>" . date('F j, Y', strtotime($date)) . "</h2>
    {$htmlContent}
</body>
</html>";

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($fullHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream to browser
        $filename = $childName . '_Worksheet_' . $date . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    // Generate worksheet content using AI
    private function generateWorksheetContent($child, $date = null) {
        $ageGroup = $child['age_group'];
        $interests = array_filter([$child['interest1'], $child['interest2']]);
        $childName = $child['name'];
        $date = $date ?? date('Y-m-d');

        // Get child's difficulty preferences from feedback
        $difficultyPreferences = $this->feedbackAPI->getChildDifficultyPreferences($child['id']);

        // Build the prompt with difficulty preferences
        $prompt = $this->buildWorksheetPrompt($childName, $ageGroup, $interests, $date, $difficultyPreferences);

        // Generate content using OpenAI
        $result = $this->openai->callApiWithoutEcho($prompt, $this->getSystemPrompt($difficultyPreferences));

        if (!$result || !isset($result['content'])) {
            throw new Exception('Failed to generate worksheet content');
        }

        return $result['content'];
    }

        // Build the worksheet generation prompt 
    private function buildWorksheetPrompt($childName, $ageGroup, $interests, $date, $difficultyPreferences = null) {
        $interestsText = implode(' and ', $interests);
        $dateFormatted = date('F j, Y', strtotime($date));

        $prompt = "Create a personalized educational worksheet for {$childName}, who is in {$ageGroup} and loves {$interestsText}. ";
        $prompt .= "The worksheet is for {$dateFormatted}. ";

        // Add difficulty adjustments based on feedback
        if ($difficultyPreferences && $difficultyPreferences['feedback_count'] > 0) {
            $mathAdjustment = $this->getDifficultyAdjustmentText($difficultyPreferences['math_preference'], 'math');
            $otherAdjustment = $this->getDifficultyAdjustmentText($difficultyPreferences['other_preference'], 'other subjects');
            
            if ($mathAdjustment) {
                $prompt .= "\nMATH DIFFICULTY: {$mathAdjustment}";
            }
            if ($otherAdjustment) {
                $prompt .= "\nOTHER SUBJECTS DIFFICULTY: {$otherAdjustment}";
            }
        }

        $prompt .= "\nCreate a worksheet that includes:\n";
        $prompt .= "1. Mathematics problems appropriate for {$ageGroup}\n";
        $prompt .= "2. English/Language Arts activities\n";
        $prompt .= "3. Science exploration questions\n";
        $prompt .= "4. Creative activities related to {$interestsText}\n";
        $prompt .= "5. Fun facts or interesting information about {$interestsText}\n";

        return $prompt;
    }

    // Get the system prompt for worksheet generation
    private function getSystemPrompt($difficultyPreferences = null) {
        $basePrompt = "You are an expert educational content creator specializing in creating engaging, age-appropriate worksheets for children. 

Create worksheets that:
- Are perfectly tailored to the child's age/grade level
- Incorporate their interests naturally into learning activities
- Include a variety of subjects (math, English, science, creativity)
- Are fun and engaging while educational
- Have clear instructions and age-appropriate language
- Include both learning and creative elements";

        // Add difficulty guidance if we have feedback data
        if ($difficultyPreferences && $difficultyPreferences['feedback_count'] > 0) {
            $basePrompt .= "\n\nIMPORTANT: Adjust difficulty based on previous feedback:";
            
            if ($difficultyPreferences['math_preference'] < -0.5) {
                $basePrompt .= "\n- Make math problems MORE challenging than typical for this age group";
            } elseif ($difficultyPreferences['math_preference'] > 0.5) {
                $basePrompt .= "\n- Make math problems EASIER than typical for this age group";
            }
            
            if ($difficultyPreferences['other_preference'] < -0.5) {
                $basePrompt .= "\n- Make reading/science/other activities MORE challenging than typical";
            } elseif ($difficultyPreferences['other_preference'] > 0.5) {
                $basePrompt .= "\n- Make reading/science/other activities EASIER than typical";
            }
        }

        $basePrompt .= "\n\nFormat the worksheet as clean, well-structured HTML with:
- A clear title with the child's name
- Distinct sections for different subjects
- Proper HTML structure (h1, h2, p, div, etc.)
- Simple styling that works well for PDF conversion
- No external CSS or JavaScript dependencies
- Print-friendly formatting

Make sure all content is safe, educational, and appropriate for children.
The HTML should be ready for PDF conversion.";

        return $basePrompt;
    }
    
    // Convert difficulty preference to human-readable adjustment text
    private function getDifficultyAdjustmentText($preference, $subject) {
        if ($preference < -1) {
            return "Make {$subject} significantly more challenging";
        } elseif ($preference < -0.5) {
            return "Make {$subject} moderately more challenging";
        } elseif ($preference > 1) {
            return "Make {$subject} significantly easier";
        } elseif ($preference > 0.5) {
            return "Make {$subject} moderately easier";
        }
        
        return null; // No adjustment needed
    }
    
    // Generate worksheet content for download (without storing in DB)
    public function generateWorksheetContentForDownload($childData, $date = null) {
        error_log("Worksheet Generation: Starting for child: " . $childData['name']);
        
        if (!$this->openai) {
            error_log("Worksheet Generation: OpenAI not configured - API key missing");
            throw new Exception('OpenAI API not configured');
        }

        $date = $date ?? date('Y-m-d');
        error_log("Worksheet Generation: Date set to: $date");
        
        // Get child's difficulty preferences from feedback
        $difficultyPreferences = $this->feedbackAPI->getChildDifficultyPreferences($childData['id']);
        error_log("Worksheet Generation: Got difficulty preferences, feedback count: " . $difficultyPreferences['feedback_count']);

        // Build the prompt with difficulty preferences
        $prompt = $this->buildWorksheetPrompt(
            $childData['name'], 
            $childData['age_group'], 
            [$childData['interest1'], $childData['interest2']], 
            $date, 
            $difficultyPreferences
        );
        error_log("Worksheet Generation: Prompt built, length: " . strlen($prompt));

        // Generate content using OpenAI
        error_log("Worksheet Generation: Calling OpenAI API");
        $result = $this->openai->callApiWithoutEcho($prompt, $this->getSystemPrompt($difficultyPreferences));

        if (!$result) {
            error_log("Worksheet Generation: OpenAI API returned null result");
            throw new Exception('Failed to connect to OpenAI API');
        }
        
        if (!isset($result['content'])) {
            error_log("Worksheet Generation: OpenAI API result missing content field: " . json_encode($result));
            throw new Exception('Failed to generate worksheet content - no content in response');
        }

        error_log("Worksheet Generation: Successfully generated content, length: " . strlen($result['content']));
        return $result['content'];
    }
    
    // Stream PDF directly from content (without storing)
    public function streamPDFToBrowserFromContent($htmlContent, $childName, $date) {
        // Add answer spacing to the content
        $pdfContent = $this->addAnswerSpacing($htmlContent);
        
        // Try TCPDF first, fallback to DOMPDF
        if (class_exists('TCPDF')) {
            $this->streamPDFWithTCPDF($pdfContent, $childName, $date);
        } elseif (class_exists('Dompdf\Dompdf')) {
            $this->streamPDFWithDOMPDF($pdfContent, $childName, $date);
        } else {
            throw new Exception('No PDF library available');
        }
    }

    // Preview worksheet content without saving
    public function previewWorksheet($userId, $childId) {
        try {
            if (!$this->openai) {
                throw new Exception('OpenAI API not configured');
            }

            // Get child information
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.plan 
                FROM children c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $userId]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$child) {
                throw new Exception('Child not found or unauthorized');
            }

            // Generate preview content
            $content = $this->generateWorksheetContent($child);

            return [
                'status' => 'success',
                'content' => $content,
                'child_name' => $child['name'],
                'age_group' => $child['age_group'],
                'interests' => [$child['interest1'], $child['interest2']]
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Generate worksheets for all children (paid users only)
    public function generateWorksheetForAllChildren($userId, $date = null) {
        try {
            $date = $date ?? date('Y-m-d');

            // Check user plan
            $stmt = $this->pdo->prepare("SELECT plan FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || $user['plan'] === 'free') {
                throw new Exception('Bulk worksheet generation requires a paid plan');
            }

            // Get all children for user
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE user_id = ?");
            $stmt->execute([$userId]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($children)) {
                throw new Exception('No children found');
            }

            $results = [];
            $successCount = 0;
            $failCount = 0;

            foreach ($children as $child) {
                try {
                    $result = $this->generateWorksheet($userId, $child['id'], $date);
                    $results[] = [
                        'child_id' => $child['id'],
                        'status' => $result['status'],
                        'message' => $result['message'] ?? null
                    ];

                    if ($result['status'] === 'success') {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } catch (Exception $e) {
                    $results[] = [
                        'child_id' => $child['id'],
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                    $failCount++;
                }
            }

            return [
                'status' => 'success',
                'total_children' => count($children),
                'successful_generations' => $successCount,
                'failed_generations' => $failCount,
                'results' => $results,
                'message' => "Generated worksheets for {$successCount} children"
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
