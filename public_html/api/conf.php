<?php
require_once 'env.php';
loadEnv();

// Database configuration
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dbPath = __DIR__ . '/database/database.sqlite';
            $dbDir = dirname($dbPath);
            
            // Create database directory if it doesn't exist
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->pdo = new PDO("sqlite:" . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch(PDOException $e) {
            exit("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function createTables() {
        // 🧑 Parent accounts
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id TEXT PRIMARY KEY,
            email TEXT UNIQUE NOT NULL,
            name TEXT,
            password_hash TEXT,
            plan TEXT NOT NULL DEFAULT 'free',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);

        // Add name column if it doesn't exist
        try {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN name TEXT");
        } catch (Exception $e) {
            // Column already exists, ignore
        }
        
        // Add magic link columns if they don't exist
        try {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN magic_token TEXT");
        } catch (Exception $e) {
            // Column already exists, ignore
        }
        
        try {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN magic_expires_at DATETIME");
        } catch (Exception $e) {
            // Column already exists, ignore
        }

        // 👧 Children (siblings)
        $sql = "CREATE TABLE IF NOT EXISTS children (
            id TEXT PRIMARY KEY,
            user_id TEXT NOT NULL,
            name TEXT NOT NULL,
            age_group INTEGER NOT NULL,
            interest1 TEXT,
            interest2 TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // 📄 Daily worksheets (grouped by day, all subjects in one)
        $sql = "CREATE TABLE IF NOT EXISTS worksheets (
            id TEXT PRIMARY KEY,
            child_id TEXT NOT NULL,
            date DATE NOT NULL,
            content TEXT NOT NULL,
            pdf_path TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
            UNIQUE(child_id, date)
        )";
        $this->pdo->exec($sql);

        // 📋 Parent feedback on worksheets
        $sql = "CREATE TABLE IF NOT EXISTS feedback (
            id TEXT PRIMARY KEY,
            worksheet_id TEXT NOT NULL,
            parent_name TEXT NOT NULL,
            parent_email TEXT NOT NULL,
            difficulty TEXT NOT NULL,
            engagement TEXT NOT NULL,
            completion TEXT NOT NULL,
            favorite_part TEXT,
            challenging_part TEXT,
            suggestions TEXT,
            would_recommend TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(worksheet_id) REFERENCES worksheets(id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // Create indexes for better performance
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_children_user_id ON children(user_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheets_child_id ON worksheets(child_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheets_date ON worksheets(date)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_feedback_worksheet_id ON feedback(worksheet_id)");
    }

    public function getPDO() {
        return $this->pdo;
    }

    // Generate random string IDs
    public static function generateUserId() {
        return 'user_' . bin2hex(random_bytes(8));
    }

    public static function generateChildId() {
        return 'child_' . bin2hex(random_bytes(8));
    }

    public static function generateWorksheetId() {
        return 'ws_' . strtoupper(bin2hex(random_bytes(4)));
    }

    public static function generateFeedbackId() {
        return 'fb_' . bin2hex(random_bytes(8));
    }
}