<?php

require_once __DIR__ . '/AiProviderInterface.php';

class OpenaiProvider implements AiProviderInterface {
    private $apiKey;
    private $model;
    private $maxRetries = 3;
    private $lastTokenUsage = ['input' => 0, 'output' => 0];
    private $lastLatency = 0;
    
    public function __construct(string $apiKey, string $model = 'gpt-4') {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }
    
    public function callApi(string $prompt, string $systemPrompt, string $outputDir): ?array {
        return $this->callApiWithoutEcho($prompt, $systemPrompt);
    }
    
    public function callApiWithoutEcho(string $prompt, string $systemPrompt): ?array {
        $retryCount = 0;
        $success = false;
        $response = null;
        $responseData = null;
        $httpCode = 0;
        
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        while ($retryCount <= $this->maxRetries && !$success) {
            if ($retryCount > 0) {
                $sleepTime = pow(2, $retryCount - 1);
                error_log("Retrying OpenAI request ($retryCount/$this->maxRetries) after {$sleepTime}s delay...");
                sleep($sleepTime);
            }
            
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $endTime = microtime(true);
            $this->lastLatency = round(($endTime - $startTime) * 1000);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $success = true;
                    break;
                }
            }
            
            if ($retryCount < $this->maxRetries) {
                error_log("OpenAI API Error - HTTP Code: " . $httpCode);
                if (!empty($error)) {
                    error_log("cURL Error: " . $error);
                }
                if (!empty($response)) {
                    error_log("Response: " . $response);
                }
            }
            
            $retryCount++;
        }
        
        if (!$success) {
            error_log("Failed to get response from OpenAI after $this->maxRetries retries.");
            return null;
        }
        
        // Store token usage
        if (isset($responseData['usage'])) {
            $this->lastTokenUsage = [
                'input' => $responseData['usage']['prompt_tokens'] ?? 0,
                'output' => $responseData['usage']['completion_tokens'] ?? 0
            ];
        }
        
        return [
            'content' => $responseData['choices'][0]['message']['content'],
            'tokens_in' => $this->lastTokenUsage['input'],
            'tokens_out' => $this->lastTokenUsage['output'],
            'latency_ms' => $this->lastLatency
        ];
    }

    public function getProviderName(): string {
        return 'openai';
    }

    public function getModelName(): string {
        return $this->model;
    }

    public function getLastTokenUsage(): array {
        return $this->lastTokenUsage;
    }

    public function getLastLatency(): int {
        return $this->lastLatency;
    }
}
?> 