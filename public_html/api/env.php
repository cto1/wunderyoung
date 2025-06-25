<?php
// env.php

function loadEnv() {
    // Determine which environment file to load based on domain
    $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    if (strpos($domain, 'demo.') === 0) {
        $envFile = __DIR__ . '/.env.demo';
    } else {
        $envFile = __DIR__ . '/.env';
    }
    
    if (!file_exists($envFile)) {
        throw new Exception('.env file not found: ' . $envFile);
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse line
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
                $value = substr($value, 1, -1);
            }

            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    
    // Initialize Sentry after environment variables are loaded
    initializeSentry();
}

function initializeSentry() {
    // Check if Sentry is available and DSN is configured
    if (!class_exists('\Sentry\init')) {
        return; // Sentry not installed, skip initialization
    }
    
    $sentryDsn = $_ENV['SENTRY_DSN'] ?? null;
    $sentryEnvironment = $_ENV['SENTRY_ENVIRONMENT'] ?? 'unknown';
    
    if ($sentryDsn) {
        \Sentry\init([
            'dsn' => $sentryDsn,
            'environment' => $sentryEnvironment,
            'traces_sample_rate' => 1.0,
        ]);
    }
}