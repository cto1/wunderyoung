<?php

interface AiProviderInterface {
    /**
     * Call the AI API with a prompt and system prompt
     * 
     * @param string $prompt The user prompt
     * @param string $systemPrompt The system prompt
     * @param string $outputDir Output directory (optional)
     * @return array|null Response array or null on failure
     */
    public function callApi(string $prompt, string $systemPrompt, string $outputDir): ?array;

    /**
     * Call the AI API without echo output
     * 
     * @param string $prompt The user prompt
     * @param string $systemPrompt The system prompt
     * @return array|null Response array or null on failure
     */
    public function callApiWithoutEcho(string $prompt, string $systemPrompt): ?array;

    /**
     * Get the provider name
     * 
     * @return string Provider name
     */
    public function getProviderName(): string;

    /**
     * Get the model name being used
     * 
     * @return string Model name
     */
    public function getModelName(): string;

    /**
     * Get the last token usage
     * 
     * @return array Token usage statistics
     */
    public function getLastTokenUsage(): array;

    /**
     * Get the last API call latency
     * 
     * @return int Latency in milliseconds
     */
    public function getLastLatency(): int;
}
?> 