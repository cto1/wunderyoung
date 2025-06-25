<?php
require_once 'env.php';
loadEnv();

require_once 'vault_conf.php';
require_once 'usage_conf.php';
require_once 'ideas_conf.php';

// config.php
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
            $this->createTable();
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

    private function createTable() {

        $sql = "CREATE TABLE IF NOT EXISTS organizations (
            org_id TEXT PRIMARY KEY,
            name TEXT DEFAULT 'ExactSum',
            logo_url TEXT DEFAULT './assets/logos/exactsum.png',
            primary_color TEXT DEFAULT 'light',
            request_group_alias TEXT DEFAULT 'Engagements',
            requestee_alias TEXT DEFAULT 'Clients',
            notification_email TEXT DEFAULT 'notifications@exactsum.com',
            notification_name_type TEXT DEFAULT 'org' CHECK(notification_name_type IN ('org', 'user')),
            support_email TEXT DEFAULT 'support@exactsum.com',
            default_view_permission TEXT DEFAULT 'owner' CHECK(default_view_permission IN ('owner', 'admin', 'user')),
            allow_delegation INTEGER DEFAULT 1 CHECK(allow_delegation IN (0, 1)),
            ai_provider TEXT DEFAULT 'openai' CHECK(ai_provider IN ('openai', 'anthropic', 'azure', 'none')),
            ai_api_key TEXT,
            ai_model TEXT DEFAULT 'gpt-4o',
            ai_temperature REAL DEFAULT 0.7,
            ai_max_tokens INTEGER DEFAULT 2000,
            ai_max_context_length INTEGER DEFAULT 15000,
            ai_system_prompt TEXT DEFAULT 'You are a helpful assistant answering queries about documents. Provide clear, concise, and accurate responses. If you''re referencing a specific part of a document, please indicate the location (e.g., page number, section, paragraph).',
            ai_enabled BOOLEAN DEFAULT 1,
            ai_feedback_required BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);


        $sql = "CREATE TABLE IF NOT EXISTS users (
            user_id TEXT PRIMARY KEY,
            org_id TEXT NOT NULL,
            name TEXT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT,
            google_id TEXT UNIQUE,
            microsoft_id TEXT UNIQUE,
            login_token TEXT,
            token_expires_at DATETIME,
            email_verified BOOLEAN DEFAULT 0,
            role TEXT DEFAULT 'owner' CHECK(role IN ('owner', 'admin', 'user')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);


        $sql = "CREATE TABLE IF NOT EXISTS api_keys (
            api_key_id TEXT PRIMARY KEY,
            api_key_string TEXT UNIQUE NOT NULL,
            org_id TEXT NOT NULL,
            created_by_user_id TEXT NOT NULL,
            permission TEXT DEFAULT 'all' CHECK(permission IN ('all', 'restricted', 'read_only')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_used_at DATETIME,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE,
            FOREIGN KEY (created_by_user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        
        $sql = "CREATE TABLE IF NOT EXISTS api_access (
            api_access_id TEXT PRIMARY KEY,
            api_key_id TEXT NOT NULL,
            api_called TEXT NOT NULL,
            payload JSON,
            response JSON,
            ip_address TEXT,
            town TEXT,
            county TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (api_key_id) REFERENCES api_keys(api_key_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);


        // Cases table with lifecycle stage
        $sql = "CREATE TABLE IF NOT EXISTS cases (
            case_id TEXT PRIMARY KEY,
            org_id TEXT NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            lifecycle_stage TEXT DEFAULT 'draft' CHECK(lifecycle_stage IN ('draft', 'active', 'archived')),
            primary_contact_email TEXT,
            due_date DATE,
            created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_by TEXT NOT NULL,
            archived_on DATETIME,
            archived_by TEXT,
            archived_reason TEXT,
            last_modified DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // Requests table remains the same
        $sql = "CREATE TABLE IF NOT EXISTS case_requests (
            request_id TEXT PRIMARY KEY,
            case_id TEXT NOT NULL,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT DEFAULT 'open' CHECK(status IN ('inactive', 'open', 'in_review', 'complete', 'returned')),
            not_applicable INTEGER DEFAULT 0 CHECK(not_applicable IN (0, 1)),
            client_email TEXT,
            created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
            due_date DATE,
            last_modified DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (case_id) REFERENCES cases(case_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS request_files (
            file_id TEXT PRIMARY KEY,
            request_id TEXT NOT NULL,
            file_url TEXT NOT NULL,
            file_name TEXT NOT NULL,
            file_type TEXT,
            file_size INTEGER,
            uploaded_by TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES case_requests(request_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS request_comments (
            comment_id TEXT PRIMARY KEY,
            request_id TEXT NOT NULL,
            comment TEXT NOT NULL,
            comment_source TEXT CHECK(comment_source IN ('upload', 'status_update')),
            comment_user_id TEXT NOT NULL,
            file_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES case_requests(request_id) ON DELETE CASCADE,
            FOREIGN KEY (file_id) REFERENCES request_files(file_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS activities (
            activity_id TEXT PRIMARY KEY,
            actor_id TEXT NOT NULL,           /* References users.user_id */
            actor_email TEXT NOT NULL,       /* References users.email or client_access_tokens.client_email */
            actor_type TEXT NOT NULL,         /* e.g., 'Requestor User', 'Client' */
            action TEXT NOT NULL,             /* e.g., 'sent request', 'fulfilled request', 'approved request' */
            request_id TEXT,         /* References case_requests.request_id */
            case_id TEXT,            /* References cases.case_id */
            org_id TEXT NOT NULL,             /* References organizations.org_id */
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (actor_id) REFERENCES users(user_id),
            FOREIGN KEY (request_id) REFERENCES case_requests(request_id) ON DELETE SET NULL,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE,
            FOREIGN KEY (case_id) REFERENCES cases(case_id) ON DELETE SET NULL
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS client_access_tokens (
            token_id TEXT PRIMARY KEY,
            case_id TEXT NOT NULL,
            client_email TEXT NOT NULL,
            access_token TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (case_id) REFERENCES cases(case_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS request_queries (
            query_id TEXT PRIMARY KEY,
            request_id TEXT NOT NULL,
            query_text TEXT NOT NULL,
            asked_by TEXT NOT NULL,
            asked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'answered', 'closed')),
            FOREIGN KEY (request_id) REFERENCES case_requests(request_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS query_responses (
            response_id TEXT PRIMARY KEY,
            query_id TEXT NOT NULL,
            parent_response_id TEXT,
            file_id TEXT,
            response_text TEXT NOT NULL,
            is_from_client BOOLEAN DEFAULT 0,
            responded_by TEXT NOT NULL,
            response_source TEXT DEFAULT 'ai' CHECK(response_source IN ('user', 'ai')),
            document_location TEXT,
            thumbs_up BOOLEAN,
            feedback_comment TEXT,
            responded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (query_id) REFERENCES request_queries(query_id) ON DELETE CASCADE,
            FOREIGN KEY (parent_response_id) REFERENCES query_responses(response_id) ON DELETE CASCADE,
            FOREIGN KEY (file_id) REFERENCES request_files(file_id) ON DELETE SET NULL
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS workflows (
            workflow_id TEXT PRIMARY KEY,
            org_id TEXT NOT NULL,
            name TEXT NOT NULL,
            trigger_type TEXT NOT NULL CHECK(trigger_type IN ('client_document_upload', 'client_comment', 'staff_comment', 'status_change', 'scheduled_reminder')),
            action_type TEXT NOT NULL CHECK(action_type IN ('send_email')),
            action_config JSON NOT NULL,
            schedule_config JSON,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS case_reminder_settings (
            setting_id TEXT PRIMARY KEY,
            case_id TEXT NOT NULL,
            frequency TEXT NOT NULL CHECK(frequency IN ('daily', 'weekly', 'monthly')),
            days_of_week TEXT, -- comma-separated list of days (e.g., 'mon,wed,fri')
            day_of_month INTEGER, -- for monthly frequency (1-31)
            hour_of_day INTEGER DEFAULT 9, -- default to 9am
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (case_id) REFERENCES cases(case_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS reminder_logs (
            log_id TEXT PRIMARY KEY,
            client_email TEXT NOT NULL,
            case_id TEXT NOT NULL,
            sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (case_id) REFERENCES cases(case_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // Login attempts table for brute force protection
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            attempt_id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
            email TEXT NOT NULL,
            ip_address TEXT NOT NULL,
            user_agent TEXT,
            action TEXT NOT NULL CHECK(action IN ('password_login', 'magic_link', 'set_password', 'change_password')),
            success BOOLEAN NOT NULL DEFAULT 0,
            reason TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )";
        $this->pdo->exec($sql);

        // Create indexes for performance
        $sql = "CREATE INDEX IF NOT EXISTS idx_login_attempts_email ON login_attempts(email)";
        $this->pdo->exec($sql);
        
        $sql = "CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address)";
        $this->pdo->exec($sql);
        
        $sql = "CREATE INDEX IF NOT EXISTS idx_login_attempts_created ON login_attempts(created_at)";
        $this->pdo->exec($sql);

        // Call other schema creation methods
        VaultDatabaseSchema::createTables($this->pdo);
        UsageDatabaseSchema::createTables($this->pdo);
        IdeasDatabaseSchema::createTables($this->pdo);
    }

    public function getPDO() {
        return $this->pdo;
    }
}