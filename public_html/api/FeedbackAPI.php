<?php

require_once 'conf.php';

class FeedbackAPI {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    // Submit feedback for a worksheet
    public function submitFeedback($data) {
        try {
            $required = ['worksheet_id', 'child_id', 'completed'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            // Verify worksheet exists and belongs to the child
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE id = ? AND child_id = ?");
            $stmt->execute([$data['worksheet_id'], $data['child_id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Worksheet not found or does not belong to this child');
            }

            // Check if feedback already exists for this worksheet
            $stmt = $this->pdo->prepare("SELECT id FROM worksheet_feedback WHERE worksheet_id = ?");
            $stmt->execute([$data['worksheet_id']]);
            $existingFeedback = $stmt->fetch();

            if ($existingFeedback) {
                // Update existing feedback
                $stmt = $this->pdo->prepare("
                    UPDATE worksheet_feedback 
                    SET completed = ?, 
                        math_difficulty = ?, 
                        english_difficulty = ?, 
                        science_difficulty = ?, 
                        other_difficulty = ?,
                        feedback_notes = ?
                    WHERE worksheet_id = ?
                ");
                
                $stmt->execute([
                    $data['completed'] ? 1 : 0,
                    $data['math_difficulty'] ?? null,
                    $data['english_difficulty'] ?? null,
                    $data['science_difficulty'] ?? null,
                    $data['other_difficulty'] ?? null,
                    $data['feedback_notes'] ?? null,
                    $data['worksheet_id']
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Feedback updated successfully'
                ];
            } else {
                // Insert new feedback
                $stmt = $this->pdo->prepare("
                    INSERT INTO worksheet_feedback 
                    (worksheet_id, child_id, completed, math_difficulty, english_difficulty, 
                     science_difficulty, other_difficulty, feedback_notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $data['worksheet_id'],
                    $data['child_id'],
                    $data['completed'] ? 1 : 0,
                    $data['math_difficulty'] ?? null,
                    $data['english_difficulty'] ?? null,
                    $data['science_difficulty'] ?? null,
                    $data['other_difficulty'] ?? null,
                    $data['feedback_notes'] ?? null
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Feedback submitted successfully'
                ];
            }

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Get feedback for a specific worksheet
    public function getFeedback($worksheetId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT wf.*, w.date, c.name as child_name
                FROM worksheet_feedback wf
                JOIN worksheets w ON wf.worksheet_id = w.id
                JOIN children c ON wf.child_id = c.id
                WHERE wf.worksheet_id = ?
            ");
            $stmt->execute([$worksheetId]);
            $feedback = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'feedback' => $feedback
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Get feedback summary for a child (for AI difficulty adjustment)
    public function getChildFeedbackSummary($childId, $limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT wf.*, w.date
                FROM worksheet_feedback wf
                JOIN worksheets w ON wf.worksheet_id = w.id
                WHERE wf.child_id = ?
                ORDER BY w.date DESC
                LIMIT ?
            ");
            $stmt->execute([$childId, $limit]);
            $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate averages and patterns
            $summary = [
                'total_worksheets' => count($feedback),
                'completion_rate' => 0,
                'math_difficulty_trend' => 'just_right',
                'english_difficulty_trend' => 'just_right',
                'science_difficulty_trend' => 'just_right',
                'other_difficulty_trend' => 'just_right'
            ];

            if (count($feedback) > 0) {
                $completed = array_filter($feedback, function($f) { return $f['completed']; });
                $summary['completion_rate'] = count($completed) / count($feedback);

                // Calculate difficulty trends
                foreach (['math', 'english', 'science', 'other'] as $subject) {
                    $difficulties = array_filter(array_column($feedback, $subject . '_difficulty'));
                    if (count($difficulties) > 0) {
                        $difficulty_counts = array_count_values($difficulties);
                        $summary[$subject . '_difficulty_trend'] = array_key_exists('hard', $difficulty_counts) && $difficulty_counts['hard'] > count($difficulties) / 2 ? 'hard' :
                            (array_key_exists('easy', $difficulty_counts) && $difficulty_counts['easy'] > count($difficulties) / 2 ? 'easy' : 'just_right');
                    }
                }
            }

            return [
                'status' => 'success',
                'feedback_summary' => $summary,
                'recent_feedback' => $feedback
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Get completion streak for a child
    public function getCompletionStreak($childId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT w.date, COALESCE(wf.completed, 0) as completed
                FROM worksheets w
                LEFT JOIN worksheet_feedback wf ON w.id = wf.worksheet_id
                WHERE w.child_id = ?
                ORDER BY w.date DESC
            ");
            $stmt->execute([$childId]);
            $worksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $streak = 0;
            $currentDate = date('Y-m-d');
            
            foreach ($worksheets as $worksheet) {
                // Only count completed worksheets
                if ($worksheet['completed']) {
                    $streak++;
                } else {
                    // Break streak on first non-completed worksheet
                    break;
                }
            }

            return [
                'status' => 'success',
                'completion_streak' => $streak
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
} 