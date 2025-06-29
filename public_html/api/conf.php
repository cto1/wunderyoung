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
            password_hash TEXT,
            plan TEXT NOT NULL DEFAULT 'free',
            is_verified INTEGER NOT NULL DEFAULT 0,
            stripe_customer_id TEXT,
            stripe_subscription_id TEXT,
            plan_ends_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);

        // ✉️ Passwordless login tokens
        $sql = "CREATE TABLE IF NOT EXISTS magic_links (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id TEXT NOT NULL,
            token TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // 👧 Children (siblings)
        $sql = "CREATE TABLE IF NOT EXISTS children (
            id TEXT PRIMARY KEY,
            user_id TEXT NOT NULL,
            name TEXT NOT NULL,
            age_group TEXT NOT NULL,
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
            pdf_path TEXT NOT NULL,
            downloaded INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
            UNIQUE(child_id, date)
        )";
        $this->pdo->exec($sql);

        // 📋 Worksheet feedback (completion and difficulty ratings)
        $sql = "CREATE TABLE IF NOT EXISTS worksheet_feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            worksheet_id TEXT NOT NULL,
            child_id TEXT NOT NULL,
            completed INTEGER DEFAULT 0,
            math_difficulty TEXT CHECK(math_difficulty IN ('easy', 'just_right', 'hard')),
            english_difficulty TEXT CHECK(english_difficulty IN ('easy', 'just_right', 'hard')),
            science_difficulty TEXT CHECK(science_difficulty IN ('easy', 'just_right', 'hard')),
            other_difficulty TEXT CHECK(other_difficulty IN ('easy', 'just_right', 'hard')),
            feedback_notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(worksheet_id) REFERENCES worksheets(id) ON DELETE CASCADE,
            FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
            UNIQUE(worksheet_id)
        )";
        $this->pdo->exec($sql);

        // 🔗 Download tokens for on-demand worksheet generation
        $sql = "CREATE TABLE IF NOT EXISTS download_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            token TEXT UNIQUE NOT NULL,
            child_id TEXT NOT NULL,
            date DATE NOT NULL,
            is_welcome INTEGER DEFAULT 0,
            expires_at DATETIME NOT NULL,
            used_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
            UNIQUE(child_id, date)
        )";
        $this->pdo->exec($sql);

        // Create indexes for better performance
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_magic_links_token ON magic_links(token)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_magic_links_user_id ON magic_links(user_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_children_user_id ON children(user_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheets_child_id ON worksheets(child_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheets_date ON worksheets(date)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheet_feedback_child_id ON worksheet_feedback(child_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_worksheet_feedback_worksheet_id ON worksheet_feedback(worksheet_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_download_tokens_token ON download_tokens(token)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_download_tokens_child_id ON download_tokens(child_id)");
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

    public static function generateDownloadToken() {
        return 'dl_' . bin2hex(random_bytes(16));
    }
}