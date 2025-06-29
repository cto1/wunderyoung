<?php

require_once 'conf.php';
require_once 'JWTAuth.php';
require_once 'DownloadTokenAPI.php';

class FeedbackAPI {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    // Submit feedback for a worksheet
    public function submitFeedback($token, $feedbackData) {
        try {
            // TODO: Validate token and get worksheet/child information
            // For now, we'll create a basic feedback storage system
            
            // Validate required fields
            $requiredFields = ['completion', 'math_difficulty', 'other_difficulty'];
            foreach ($requiredFields as $field) {
                if (!isset($feedbackData[$field]) || empty($feedbackData[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            // Get worksheet information from token (placeholder for now)
            $worksheetInfo = $this->getWorksheetFromToken($token);
            if (!$worksheetInfo) {
                throw new Exception('Invalid or expired download token');
            }
            
            // Store feedback in database (using existing table structure)
            $stmt = $this->pdo->prepare("
                INSERT INTO worksheet_feedback (
                    worksheet_id, child_id, completed,
                    math_difficulty, english_difficulty, science_difficulty, other_difficulty,
                    feedback_notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Convert completion to integer (1 = completed, 0 = not completed)
            $completed = in_array($feedbackData['completion'], ['completed', 'mostly', 'first_time']) ? 1 : 0;
            
            $result = $stmt->execute([
                $worksheetInfo['worksheet_id'],
                $worksheetInfo['child_id'],
                $completed,
                $feedbackData['math_difficulty'],
                $feedbackData['other_difficulty'], // Use as english_difficulty
                '', // science_difficulty (not collected separately)
                $feedbackData['other_difficulty'],
                $feedbackData['comments'] ?? ''
            ]);
            
            if (!$result) {
                throw new Exception('Failed to save feedback');
            }
            
            // Update child's learning progress based on feedback
            $this->updateLearningProgress($worksheetInfo['child_id'], $feedbackData);
            
            return [
                'status' => 'success',
                'message' => 'Feedback submitted successfully',
                'feedback_id' => $this->pdo->lastInsertId()
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get worksheet information from download token
    private function getWorksheetFromToken($token) {
        try {
            $tokenAPI = new DownloadTokenAPI();
            $tokenResult = $tokenAPI->getDownloadTokenInfo($token);
            
            if ($tokenResult['status'] !== 'success') {
                throw new Exception($tokenResult['message']);
            }
            
            $tokenData = $tokenResult['token_data'];
            
            // Create worksheet ID from child_id and date (or generate new one)
            $worksheetId = Database::generateWorksheetId();
            
            return [
                'worksheet_id' => $worksheetId,
                'child_id' => $tokenData['child_id'],
                'child_name' => $tokenData['child_name'],
                'date' => $tokenData['date'],
                'token_data' => $tokenData
            ];
            
        } catch (Exception $e) {
            error_log("Error getting worksheet from token: " . $e->getMessage());
            return null;
        }
    }
    
    // Update child's learning progress based on feedback
    private function updateLearningProgress($childId, $feedbackData) {
        try {
            // Calculate completion score (for streak tracking)
            $completionScore = $this->calculateCompletionScore($feedbackData['completion']);
            
            // Store learning analytics
            $stmt = $this->pdo->prepare("
                INSERT INTO learning_analytics (
                    child_id, date, completion_score,
                    math_difficulty_rating, other_difficulty_rating,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    completion_score = VALUES(completion_score),
                    math_difficulty_rating = VALUES(math_difficulty_rating),
                    other_difficulty_rating = VALUES(other_difficulty_rating)
            ");
            
            $stmt->execute([
                $childId,
                date('Y-m-d'),
                $completionScore,
                $feedbackData['math_difficulty'],
                $feedbackData['other_difficulty']
            ]);
            
            // Update child's difficulty preferences for AI prompts
            $this->updateDifficultyPreferences($childId, $feedbackData);
            
        } catch (Exception $e) {
            error_log("Error updating learning progress: " . $e->getMessage());
        }
    }
    
    // Calculate completion score for streak tracking
    private function calculateCompletionScore($completion) {
        switch ($completion) {
            case 'completed': return 100;
            case 'mostly': return 75;
            case 'some': return 50;
            case 'none': return 0;
            case 'first_time': return 100; // Count first worksheet as completed
            default: return 0;
        }
    }
    
    // Update difficulty preferences for future AI prompts
    private function updateDifficultyPreferences($childId, $feedbackData) {
        try {
            // Get or create difficulty preferences for this child
            $stmt = $this->pdo->prepare("
                SELECT * FROM child_difficulty_preferences WHERE child_id = ?
            ");
            $stmt->execute([$childId]);
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$preferences) {
                // Create new preferences record
                $stmt = $this->pdo->prepare("
                    INSERT INTO child_difficulty_preferences (
                        child_id, math_preference, other_preference, 
                        feedback_count, created_at, updated_at
                    ) VALUES (?, ?, ?, 1, NOW(), NOW())
                ");
                $stmt->execute([
                    $childId,
                    $feedbackData['math_difficulty'],
                    $feedbackData['other_difficulty']
                ]);
            } else {
                // Update existing preferences with weighted average
                $feedbackCount = $preferences['feedback_count'] + 1;
                $weight = 0.3; // How much the new feedback influences the preference
                
                $newMathPreference = $this->adjustDifficultyPreference(
                    $preferences['math_preference'], 
                    $feedbackData['math_difficulty'], 
                    $weight
                );
                
                $newOtherPreference = $this->adjustDifficultyPreference(
                    $preferences['other_preference'], 
                    $feedbackData['other_difficulty'], 
                    $weight
                );
                
                $stmt = $this->pdo->prepare("
                    UPDATE child_difficulty_preferences 
                    SET math_preference = ?, other_preference = ?, 
                        feedback_count = ?, updated_at = NOW()
                    WHERE child_id = ?
                ");
                $stmt->execute([
                    $newMathPreference,
                    $newOtherPreference,
                    $feedbackCount,
                    $childId
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error updating difficulty preferences: " . $e->getMessage());
        }
    }
    
    // Adjust difficulty preference based on feedback
    private function adjustDifficultyPreference($currentPreference, $newFeedback, $weight) {
        // Convert feedback to numeric values
        $feedbackValue = match($newFeedback) {
            'easy' => -1,      // Make it harder
            'just_right' => 0, // Keep same difficulty
            'hard' => 1,       // Make it easier
            default => 0
        };
        
        // Adjust current preference
        $adjustment = $feedbackValue * $weight;
        $newPreference = $currentPreference + $adjustment;
        
        // Keep within reasonable bounds (-2 to +2)
        return max(-2, min(2, $newPreference));
    }
    
    // Get child's difficulty preferences for AI prompt adjustment
    public function getChildDifficultyPreferences($childId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT math_preference, other_preference, feedback_count
                FROM child_difficulty_preferences 
                WHERE child_id = ?
            ");
            $stmt->execute([$childId]);
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$preferences) {
                return [
                    'math_preference' => 0,
                    'other_preference' => 0,
                    'feedback_count' => 0
                ];
            }
            
            return $preferences;
            
        } catch (Exception $e) {
            return [
                'math_preference' => 0,
                'other_preference' => 0,
                'feedback_count' => 0
            ];
        }
    }
    
    // Get learning streak for a child
    public function getLearningStreak($childId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT date, completion_score 
                FROM learning_analytics 
                WHERE child_id = ? AND completion_score >= 50
                ORDER BY date DESC
            ");
            $stmt->execute([$childId]);
            $completedDays = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($completedDays)) {
                return 0;
            }
            
            // Calculate consecutive days streak
            $streak = 0;
            $currentDate = new DateTime();
            $currentDate->setTime(0, 0, 0);
            
            // Check if today or yesterday has completion
            $today = $currentDate->format('Y-m-d');
            $yesterday = (clone $currentDate)->modify('-1 day')->format('Y-m-d');
            
            $hasRecentCompletion = false;
            foreach ($completedDays as $day) {
                if ($day['date'] === $today || $day['date'] === $yesterday) {
                    $hasRecentCompletion = true;
                    break;
                }
            }
            
            if (!$hasRecentCompletion) {
                return 0; // Streak broken if no completion today or yesterday
            }
            
            // Count consecutive days from most recent
            $checkDate = new DateTime($completedDays[0]['date']);
            
            foreach ($completedDays as $day) {
                $dayDate = new DateTime($day['date']);
                
                if ($dayDate->format('Y-m-d') === $checkDate->format('Y-m-d')) {
                    $streak++;
                    $checkDate->modify('-1 day');
                } else {
                    break; // Gap found, streak ends
                }
            }
            
            return $streak;
            
        } catch (Exception $e) {
            return 0;
        }
    }
}

// Handle API requests - only execute if this file is being accessed directly
if (basename($_SERVER['SCRIPT_NAME']) === 'FeedbackAPI.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $api = new FeedbackAPI();
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid JSON input'
            ]);
            exit;
        }
        
        $token = $input['token'] ?? '';
        if (empty($token)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing download token'
            ]);
            exit;
        }
        
        $result = $api->submitFeedback($token, $input);
        
        echo json_encode($result);
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $api = new FeedbackAPI();
        
        // Handle streak calculation requests
        if (isset($_GET['child_id']) && isset($_GET['action']) && $_GET['action'] === 'streak') {
            $childId = intval($_GET['child_id']);
            $streak = $api->getLearningStreak($childId);
            
            echo json_encode([
                'status' => 'success',
                'streak' => $streak
            ]);
        }
        // Handle difficulty preferences requests
        elseif (isset($_GET['child_id']) && isset($_GET['action']) && $_GET['action'] === 'preferences') {
            $childId = intval($_GET['child_id']);
            $preferences = $api->getChildDifficultyPreferences($childId);
            
            echo json_encode([
                'status' => 'success',
                'preferences' => $preferences
            ]);
        }
        else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request parameters'
            ]);
        }
    }
    else {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed'
        ]);
    }
} 