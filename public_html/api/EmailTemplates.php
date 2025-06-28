<?php

class EmailTemplates {
    private $templateDir;
    
    public function __construct() {
        $this->templateDir = __DIR__ . '/templates/';
    }
    
    /**
     * Load and process an email template
     * @param string $templateName - Name of template (without extension)
     * @param array $variables - Variables to replace in template
     * @return array - Returns ['subject' => '', 'html' => '', 'text' => '']
     */
    public function getTemplate($templateName, $variables = []) {
        $htmlFile = $this->templateDir . $templateName . '.html';
        $textFile = $this->templateDir . $templateName . '.txt';
        
        if (!file_exists($htmlFile) || !file_exists($textFile)) {
            throw new Exception("Email template '{$templateName}' not found");
        }
        
        $htmlContent = file_get_contents($htmlFile);
        $textContent = file_get_contents($textFile);
        
        // Replace variables in both templates
        foreach ($variables as $key => $value) {
            $placeholder = '{{' . strtoupper($key) . '}}';
            $htmlContent = str_replace($placeholder, htmlspecialchars($value), $htmlContent);
            $textContent = str_replace($placeholder, $value, $textContent);
        }
        
        // Extract subject from HTML title tag or use default
        $subject = $this->extractSubject($htmlContent, $templateName);
        
        return [
            'subject' => $subject,
            'html' => $htmlContent,
            'text' => $textContent
        ];
    }
    
    /**
     * Extract subject from HTML title tag
     */
    private function extractSubject($htmlContent, $templateName) {
        if (preg_match('/<title>(.*?)<\/title>/i', $htmlContent, $matches)) {
            return trim($matches[1]);
        }
        
        // Fallback subjects
        $subjects = [
            'welcome-email' => 'Welcome to Yes Homework - Your AI-Powered Learning Companion!',
            'login-email' => 'Your Daily Homework Login Link'
        ];
        
        return $subjects[$templateName] ?? 'Yes Homework Notification';
    }
    
    /**
     * Get welcome email template
     */
    public function getWelcomeEmail($email, $loginLink) {
        return $this->getTemplate('welcome-email', [
            'email' => $email,
            'login_link' => $loginLink
        ]);
    }
    
    /**
     * Get login email template
     */
    public function getLoginEmail($email, $loginLink) {
        return $this->getTemplate('login-email', [
            'email' => $email,
            'login_link' => $loginLink
        ]);
    }
}
