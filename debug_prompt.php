<?php
require_once 'public_html/api/conf.php';
require_once 'public_html/api/env.php';
loadEnv();

class PromptDebugger {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }
    
    private function getPastWorksheets($childId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT content, date 
            FROM worksheets 
            WHERE child_id = ? 
            ORDER BY date DESC 
            LIMIT ?
        ");
        $stmt->execute([$childId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function extractQuestionsFromContent($htmlContent) {
        $questions = ['math' => [], 'english' => []];
        
        // Parse HTML and extract questions
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
        $xpath = new DOMXPath($dom);
        
        // Reset and extract questions properly
        $allOls = $xpath->query('//ol');
        $olIndex = 0;
        
        foreach ($allOls as $ol) {
            $items = $xpath->query('.//li', $ol);
            $section = $olIndex === 0 ? 'math' : 'english';
            
            foreach ($items as $item) {
                $questionText = trim($item->textContent);
                if (!empty($questionText) && strlen($questionText) > 3) {
                    $questions[$section][] = $questionText;
                }
            }
            $olIndex++;
        }
        
        return $questions;
    }
    
    private function buildWorksheetPrompt($childName, $ageGroup, $interests, $date, $pastWorksheets = []) {
        $interestText = implode(' and ', array_filter($interests));
        
        // Build context from past worksheets
        $pastContext = "";
        if (!empty($pastWorksheets)) {
            $pastContext = "\n\n🚫 CRITICAL: DO NOT REPEAT ANY OF THESE PREVIOUS QUESTIONS 🚫\n";
            $pastContext .= "=== QUESTIONS ALREADY USED (MUST AVOID) ===\n";
            
            foreach ($pastWorksheets as $worksheet) {
                $pastContext .= "📅 Worksheet from {$worksheet['date']}:\n";
                
                // Extract individual questions more precisely
                $questions = $this->extractQuestionsFromContent($worksheet['content']);
                if (!empty($questions)) {
                    $pastContext .= "Math questions used:\n";
                    foreach ($questions['math'] as $q) {
                        $pastContext .= "• " . trim($q) . "\n";
                    }
                    $pastContext .= "English questions used:\n";
                    foreach ($questions['english'] as $q) {
                        $pastContext .= "• " . trim($q) . "\n";
                    }
                }
                $pastContext .= "\n";
            }
            
            $pastContext .= "=== END USED QUESTIONS ===\n\n";
            $pastContext .= "⚠️ MANDATORY REQUIREMENTS:\n";
            $pastContext .= "1. Create COMPLETELY DIFFERENT questions from those listed above\n";
            $pastContext .= "2. Use DIFFERENT numbers, operations, and word problems\n";
            $pastContext .= "3. Vary the question types and formats\n";
            $pastContext .= "4. If any question looks similar to above, rewrite it completely\n\n";
        }
        
        return "Create worksheet content for {$childName}, age {$ageGroup}, who loves {$interestText}.{$pastContext}

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
        - Some questions should be equations like 13+6= or 5*2=
        - Each question 1-2 lines maximum
        - Use {$interestText} themes in 3-4 questions maximum in Math and English
        - Age-appropriate for {$ageGroup} year olds
        
        🔥 CRITICAL REQUIREMENTS FOR UNIQUENESS:
        - Every single question MUST be completely different from previous worksheets
        - Use different numbers, different operations, different scenarios
        - Vary question formats: word problems, equations, fill-in-blanks, multiple choice
        - If creating math equations, use different number combinations
        - For English questions, use different topics, grammar points, and vocabulary
        - Think creatively to ensure NO repetition whatsoever";
    }
    
    private function getSystemPrompt() {
        return "You are an educational content creator specializing in creating UNIQUE, NON-REPETITIVE worksheets.

        🎯 PRIMARY OBJECTIVES:
        1. Follow instructions exactly
        2. Create completely original questions that avoid ANY repetition from past worksheets
        3. Ensure maximum variety and creativity in question design

        CRITICAL: Return ONLY HTML body content. NO DOCTYPE, NO <html>, NO <head>, NO <style> tags.
        
        Use only: <h3>, <ol>, <li>, <p> tags.
        
        ⚠️ UNIQUENESS MANDATE: If you see previous questions listed, you MUST create entirely different questions. 
        Think of new scenarios, different numbers, alternative formats, and creative approaches.
        
        Generate exactly what is requested - no additional content or explanations.";
    }
    
    public function showPromptForChild($childId) {
        // Get child info
        $stmt = $this->pdo->prepare("SELECT * FROM children WHERE id = ?");
        $stmt->execute([$childId]);
        $child = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$child) {
            echo "Child not found with ID: $childId\n";
            return;
        }
        
        echo "=== CHILD INFO ===\n";
        echo "Name: {$child['name']}\n";
        echo "Age: {$child['age_group']}\n";
        echo "Interests: {$child['interest1']}, {$child['interest2']}\n\n";
        
        // Get past worksheets
        $pastWorksheets = $this->getPastWorksheets($childId, 5);
        
        echo "=== PAST WORKSHEETS FOUND ===\n";
        echo "Number of past worksheets: " . count($pastWorksheets) . "\n";
        foreach ($pastWorksheets as $i => $worksheet) {
            echo ($i + 1) . ". Date: {$worksheet['date']}\n";
        }
        echo "\n";
        
        // Generate the prompt
        $date = date('Y-m-d');
        $interests = [$child['interest1'], $child['interest2']];
        
        $userPrompt = $this->buildWorksheetPrompt($child['name'], $child['age_group'], $interests, $date, $pastWorksheets);
        $systemPrompt = $this->getSystemPrompt();
        
        echo "=== SYSTEM PROMPT ===\n";
        echo $systemPrompt . "\n\n";
        
        echo "=== USER PROMPT ===\n";
        echo $userPrompt . "\n\n";
        
        echo "=== PROMPT STATS ===\n";
        echo "System prompt length: " . strlen($systemPrompt) . " characters\n";
        echo "User prompt length: " . strlen($userPrompt) . " characters\n";
        echo "Total prompt length: " . (strlen($systemPrompt) + strlen($userPrompt)) . " characters\n";
    }
    
    public function listChildren() {
        $stmt = $this->pdo->prepare("SELECT id, name, age_group FROM children ORDER BY name");
        $stmt->execute();
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "=== AVAILABLE CHILDREN ===\n";
        foreach ($children as $child) {
            echo "ID: {$child['id']} | Name: {$child['name']} | Age: {$child['age_group']}\n";
        }
        echo "\n";
    }
}

// Usage
$debugger = new PromptDebugger();

// Check command line arguments
if ($argc < 2) {
    echo "Usage: php debug_prompt.php [child_id|list]\n";
    echo "Examples:\n";
    echo "  php debug_prompt.php list              # Show all children\n";
    echo "  php debug_prompt.php child_123         # Show prompt for specific child\n";
    exit(1);
}

$command = $argv[1];

if ($command === 'list') {
    $debugger->listChildren();
} else {
    $debugger->showPromptForChild($command);
}
?>