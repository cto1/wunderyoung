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
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
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
            user_id INTEGER NOT NULL,
            token TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // 👧 Children (siblings)
        $sql = "CREATE TABLE IF NOT EXISTS children (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
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
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            child_id INTEGER NOT NULL,
            date DATE NOT NULL,
            content TEXT NOT NULL,
            pdf_path TEXT NOT NULL,
            downloaded INTEGER DEFAULT 0,
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
    }

    public function getPDO() {
        return $this->pdo;
    }
}