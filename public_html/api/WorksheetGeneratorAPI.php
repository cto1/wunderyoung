<?php

require_once 'conf.php';
require_once 'OpenaiProvider.php';

class WorksheetGeneratorAPI {
    private $db;
    private $pdo;
    private $openaiProvider;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
        
        // Initialize OpenAI provider
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new Exception('OPENAI_API_KEY not configured');
        }
        $this->openaiProvider = new OpenaiProvider($apiKey, 'gpt-4');
    }

    // Generate worksheet content for a child
    public function generateWorksheet($userId, $childId, $date = null) {
        try {
            // Verify user owns the child
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.plan 
                FROM children c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $userId]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$child) {
                throw new Exception('Child not found or access denied');
            }

            // Use provided date or today
            $worksheetDate = $date ?? date('Y-m-d');

            // Check if worksheet already exists for this date
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE child_id = ? AND date = ?");
            $stmt->execute([$childId, $worksheetDate]);
            if ($stmt->fetch()) {
                throw new Exception('Worksheet already exists for this date');
            }

            // Generate personalized content based on child's details
            $content = $this->generatePersonalizedContent($child);

            // Create the worksheet
            $worksheetId = Database::generateWorksheetId();
            $pdfPath = 'worksheets/' . $childId . '/' . $worksheetDate . '.pdf';
            
            $stmt = $this->pdo->prepare("
                INSERT INTO worksheets (id, child_id, date, content, pdf_path) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $worksheetId,
                $childId,
                $worksheetDate,
                $content,
                $pdfPath
            ]);

            return [
                'status' => 'success',
                'worksheet_id' => $worksheetId,
                'child_name' => $child['name'],
                'age_group' => $child['age_group'],
                'date' => $worksheetDate,
                'content' => $content,
                'pdf_path' => $pdfPath,
                'message' => 'Personalized worksheet generated successfully'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Generate personalized content using OpenAI
    private function generatePersonalizedContent($child) {
        // Determine age group and create appropriate prompt
        $ageGroup = strtolower($child['age_group']);
        $name = $child['name'];
        $interest1 = $child['interest1'] ?? 'animals';
        $interest2 = $child['interest2'] ?? 'stories';

        // Create prompts based on age group
        $prompt = $this->createPromptForAgeGroup($ageGroup, $name, $interest1, $interest2);
        
        $systemPrompt = "You are an expert UK primary school teacher creating engaging, curriculum-aligned worksheets. 
Create content in markdown format that can be easily converted to PDF. 
Use clear headings, bullet points, and simple formatting. 
Make the worksheet fun, educational, and appropriate for the UK curriculum.
Include the child's name and interests throughout to make it personal and engaging.";

        // Call OpenAI API
        $result = $this->openaiProvider->callApiWithoutEcho($prompt, $systemPrompt);
        
        if (!$result || !isset($result['content'])) {
            throw new Exception('Failed to generate worksheet content');
        }

        return $result['content'];
    }

    // Create age-appropriate prompts
    private function createPromptForAgeGroup($ageGroup, $name, $interest1, $interest2) {
        switch (true) {
            case (strpos($ageGroup, 'reception') !== false || strpos($ageGroup, 'eyfs') !== false):
                return "Create a one-page printable worksheet for a Reception child (age 4–5) in the UK.
Use the name [{$name}]. Include simple number practice (counting or tracing 1–10), an easy phonics or letter tracing activity, and one fun Spanish word or picture to learn.
Use large, clear text and keep instructions short and friendly.
The worksheet should feel playful and use the topics [{$interest1}] and [{$interest2}] if possible.
Format in markdown with clear sections and simple instructions.";

            case (strpos($ageGroup, 'year 1') !== false || strpos($ageGroup, 'year 2') !== false || strpos($ageGroup, 'ks1') !== false):
                return "Create a one-page printable worksheet for a KS1 child (Years 1–2, ages 5–7) in the UK.
Use the name [{$name}]. Include 3–5 short tasks:
- One simple maths problem (like adding or subtracting within 20)
- One short sentence to read or copy
- One spelling or phonics task
- One Spanish word or short phrase with an English translation
Use clear, friendly language and make it fun, using the topics [{$interest1}] and [{$interest2}] if possible.
Format in markdown with clear sections and age-appropriate activities.";

            case (strpos($ageGroup, 'year 3') !== false || strpos($ageGroup, 'year 4') !== false || 
                  strpos($ageGroup, 'year 5') !== false || strpos($ageGroup, 'year 6') !== false || 
                  strpos($ageGroup, 'ks2') !== false):
                return "Create a one-page printable worksheet for a KS2 child (Years 3–6, ages 7–11) in the UK.
Use the name [{$name}]. Include 3–5 activities:
- A slightly harder maths problem (like times tables or short word problem)
- A short writing or sentence-building task
- One grammar or spelling question
- One Spanish word or simple sentence to translate to English
Use age-appropriate UK curriculum style and keep it fun and interesting. Include the topics [{$interest1}] and [{$interest2}] if possible.
Format in markdown with clear headings and structured activities.";

            default:
                // Default to KS1 if age group not recognized
                return "Create a one-page printable worksheet for a primary school child in the UK.
Use the name [{$name}]. Include basic maths, reading, and one Spanish word.
Make it fun using the topics [{$interest1}] and [{$interest2}].
Format in markdown with clear sections.";
        }
    }

    // Generate worksheet for all children of a user (for paid users)
    public function generateWorksheetForAllChildren($userId, $date = null) {
        try {
            // Check if user has a paid plan
            $stmt = $this->pdo->prepare("SELECT plan FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('User not found');
            }

            if ($user['plan'] === 'free') {
                throw new Exception('Bulk worksheet generation requires a paid plan');
            }

            // Get all children for the user
            $stmt = $this->pdo->prepare("SELECT * FROM children WHERE user_id = ?");
            $stmt->execute([$userId]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($children)) {
                throw new Exception('No children found');
            }

            $results = [];
            $worksheetDate = $date ?? date('Y-m-d');

            foreach ($children as $child) {
                $result = $this->generateWorksheet($userId, $child['id'], $worksheetDate);
                $results[] = [
                    'child_id' => $child['id'],
                    'child_name' => $child['name'],
                    'result' => $result
                ];
            }

            return [
                'status' => 'success',
                'date' => $worksheetDate,
                'worksheets_generated' => count($results),
                'results' => $results,
                'message' => 'Worksheets generated for all children'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Preview worksheet content without saving (for testing)
    public function previewWorksheet($userId, $childId) {
        try {
            // Verify user owns the child
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.plan 
                FROM children c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$childId, $userId]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$child) {
                throw new Exception('Child not found or access denied');
            }

            // Generate content without saving
            $content = $this->generatePersonalizedContent($child);

            return [
                'status' => 'success',
                'child_name' => $child['name'],
                'age_group' => $child['age_group'],
                'interests' => [$child['interest1'], $child['interest2']],
                'content' => $content,
                'message' => 'Worksheet preview generated'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?> 