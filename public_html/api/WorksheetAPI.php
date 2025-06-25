<?php

require_once 'conf.php';

class WorksheetAPI {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    // 1. Create worksheet
    public function createWorksheet($data) {
        try {
            if (!isset($data['child_id']) || !isset($data['date']) || !isset($data['content'])) {
                throw new Exception('Child ID, date, and content are required');
            }

            // Verify child exists
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ?");
            $stmt->execute([$data['child_id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }

            // Check if worksheet already exists for this date
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE child_id = ? AND date = ?");
            $stmt->execute([$data['child_id'], $data['date']]);
            if ($stmt->fetch()) {
                throw new Exception('Worksheet already exists for this date');
            }

            // Generate worksheet ID
            $worksheetId = Database::generateWorksheetId();
            $stmt = $this->pdo->prepare("
                INSERT INTO worksheets (id, child_id, date, content, pdf_path) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $pdfPath = 'worksheets/' . $data['child_id'] . '/' . $data['date'] . '.pdf';
            
            $stmt->execute([
                $worksheetId,
                $data['child_id'],
                $data['date'],
                $data['content'],
                $pdfPath
            ]);

            return [
                'status' => 'success',
                'worksheet_id' => $worksheetId,
                'pdf_path' => $pdfPath,
                'message' => 'Worksheet created successfully'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 2. Get worksheets for a child
    public function getWorksheets($childId, $limit = 30) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name, c.age_group
                FROM worksheets w
                JOIN children c ON w.child_id = c.id
                WHERE w.child_id = ?
                ORDER BY w.date DESC
                LIMIT ?
            ");
            $stmt->execute([$childId, $limit]);
            $worksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'worksheets' => $worksheets,
                'total' => count($worksheets)
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 3. Get worksheet by ID
    public function getWorksheet($worksheetId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name, c.age_group, c.user_id
                FROM worksheets w
                JOIN children c ON w.child_id = c.id
                WHERE w.id = ?
            ");
            $stmt->execute([$worksheetId]);
            $worksheet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$worksheet) {
                throw new Exception('Worksheet not found');
            }

            return [
                'status' => 'success',
                'worksheet' => $worksheet
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 4. Update worksheet
    public function updateWorksheet($worksheetId, $data) {
        try {
            // Check if worksheet exists
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE id = ?");
            $stmt->execute([$worksheetId]);
            if (!$stmt->fetch()) {
                throw new Exception('Worksheet not found');
            }

            $updates = [];
            $params = [':id' => $worksheetId];

            if (isset($data['date'])) {
                $updates[] = "date = :date";
                $params[':date'] = $data['date'];
            }

            if (isset($data['content'])) {
                $updates[] = "content = :content";
                $params[':content'] = $data['content'];
            }

            if (isset($data['pdf_path'])) {
                $updates[] = "pdf_path = :pdf_path";
                $params[':pdf_path'] = $data['pdf_path'];
            }

            if (isset($data['downloaded'])) {
                $updates[] = "downloaded = :downloaded";
                $params[':downloaded'] = $data['downloaded'] ? 1 : 0;
            }

            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $sql = "UPDATE worksheets SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ['status' => 'success', 'message' => 'Worksheet updated successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 5. Delete worksheet
    public function deleteWorksheet($worksheetId) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE id = ?");
            $stmt->execute([$worksheetId]);
            if (!$stmt->fetch()) {
                throw new Exception('Worksheet not found');
            }

            $stmt = $this->pdo->prepare("DELETE FROM worksheets WHERE id = ?");
            $stmt->execute([$worksheetId]);

            return ['status' => 'success', 'message' => 'Worksheet deleted successfully'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 6. Get all worksheets for a user (across all children)
    public function getUserWorksheets($userId, $limit = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT w.*, c.name as child_name, c.age_group
                FROM worksheets w
                JOIN children c ON w.child_id = c.id
                WHERE c.user_id = ?
                ORDER BY w.date DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $worksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'worksheets' => $worksheets,
                'total' => count($worksheets)
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 7. Mark worksheet as downloaded
    public function markAsDownloaded($worksheetId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE worksheets SET downloaded = 1 WHERE id = ?");
            $stmt->execute([$worksheetId]);

            return ['status' => 'success', 'message' => 'Worksheet marked as downloaded'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // 8. Get worksheet statistics
    public function getWorksheetStats($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_worksheets,
                    SUM(CASE WHEN downloaded = 1 THEN 1 ELSE 0 END) as downloaded_count,
                    COUNT(DISTINCT child_id) as children_count,
                    MIN(date) as first_worksheet_date,
                    MAX(date) as latest_worksheet_date
                FROM worksheets w
                JOIN children c ON w.child_id = c.id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'stats' => $stats
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
} 