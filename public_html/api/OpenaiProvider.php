<?php

require_once __DIR__ . '/AiProviderInterface.php';

class OpenaiProvider implements AiProviderInterface {
    private $apiKey;
    private $model;
    private $maxRetries = 3;
    private $lastTokenUsage = ['input' => 0, 'output' => 0];
    private $lastLatency = 0;
    
    public function __construct(string $apiKey, string $model = 'gpt-4-turbo-preview') {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }
    
    public function callApi(string $prompt, string $systemPrompt, string $outputDir): ?array {
        //echo "\n====== OpenAI ======\n";
        //echo "Sending request to OpenAI...\n";
        
        $result = $this->callApiWithoutEcho($prompt, $systemPrompt);
        if (!$result) {
            return null;
        }
        
        return $result;
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
            'temperature' => 0.2,
            'max_tokens' => 1000
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        while ($retryCount <= $this->maxRetries && !$success) {
            if ($retryCount > 0) {
                $sleepTime = pow(2, $retryCount - 1);
                echo "Retrying OpenAI request ($retryCount/$this->maxRetries) after {$sleepTime}s delay...\n";
                sleep($sleepTime);
            }
            
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
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
                echo "HTTP Code: " . $httpCode . "\n";
                if (!empty($error)) {
                    echo "cURL Error: " . $error . "\n";
                }
                if (!empty($response)) {
                    echo "Response: " . $response . "\n";
                }
            }
            
            $retryCount++;
        }
        
        if (!$success) {
            echo "Failed to get response from OpenAI after $this->maxRetries retries.\n";
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

    public function getAvailableModels(): array {
        $retryCount = 0;
        $success = false;
        $response = null;
        $responseData = null;
        $httpCode = 0;
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        while ($retryCount <= $this->maxRetries && !$success) {
            if ($retryCount > 0) {
                $sleepTime = pow(2, $retryCount - 1);
                echo "Retrying OpenAI models request ($retryCount/$this->maxRetries) after {$sleepTime}s delay...\n";
                sleep($sleepTime);
            }
            
            $ch = curl_init('https://api.openai.com/v1/models');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                if (isset($responseData['data'])) {
                    $success = true;
                    break;
                }
            }
            
            if ($retryCount < $this->maxRetries) {
                echo "HTTP Code: " . $httpCode . "\n";
                if (!empty($error)) {
                    echo "cURL Error: " . $error . "\n";
                }
                if (!empty($response)) {
                    echo "Response: " . $response . "\n";
                }
            }
            
            $retryCount++;
        }
        
        if (!$success) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch models from OpenAI',
                'models' => []
            ];
        }
        
        $models = [];
        foreach ($responseData['data'] as $model) {
            // Only include chat models
            if (strpos($model['id'], 'gpt') === 0) {
                $models[] = [
                    'id' => $model['id'],
                    'name' => $model['id'],
                    'type' => 'chat',
                    'context_length' => $model['context_length'] ?? null,
                    'description' => $model['description'] ?? null
                ];
            }
        }
        
        return [
            'status' => 'success',
            'models' => $models
        ];
    }
} 