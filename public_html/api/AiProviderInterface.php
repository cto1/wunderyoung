<?php

interface AiProviderInterface {
    /**
     * Call the AI API and save response to a file
     * @param string $prompt The user prompt
     * @param string $systemPrompt The system prompt
     * @param string $outputDir Directory to save the response
     * @return array|null Response data or null on failure
     */
    public function callApi(string $prompt, string $systemPrompt, string $outputDir): ?array;
    
    /**
     * Call the AI API without saving to file
     * @param string $prompt The user prompt
     * @param string $systemPrompt The system prompt
     * @return array|null Response data or null on failure
     */
    public function callApiWithoutEcho(string $prompt, string $systemPrompt): ?array;
    
    /**
     * Get the provider name
     * @return string Provider name
     */
    public function getProviderName(): string;
    
    /**
     * Get the model name
     * @return string Model name
     */
    public function getModelName(): string;
    
    /**
     * Get last token usage
     * @return array Token usage data
     */
    public function getLastTokenUsage(): array;
    
    /**
     * Get last request latency
     * @return int Latency in milliseconds
     */
    public function getLastLatency(): int;
}
?> 