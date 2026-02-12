<?php

namespace Modules\AIIntegration\Actions;

use CController;
use Modules\AIIntegration\ConfigStorage;
use Modules\AIIntegration\AIProviderHelper;

/**
 * Handle AI query requests from quick actions
 * Uses AIProviderHelper for consistent API calls across all providers
 */
class CControllerAIQuery extends CController {
    
    protected function init(): void {
        $this->disableCsrfValidation();
    }
    
    protected function checkInput(): bool {
        return true;
    }
    
    protected function checkPermissions(): bool {
        // Available to all authenticated users
        return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
    }
    
    protected function doAction(): void {
        // Clean output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        try {
            // Get JSON input
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                throw new \Exception('Invalid JSON input');
            }
            
            error_log('AI Integration Query - Request: ' . print_r($data, true));
            
            $question = $data['question'] ?? '';
            $provider = $data['provider'] ?? '';
            $context = $data['context'] ?? [];
            
            // Validate question
            if (empty($question)) {
                throw new \Exception('Question is required');
            }
            
            // Load configuration
            $config = ConfigStorage::load();
            
            // Determine provider
            if (empty($provider) || $provider === 'undefined') {
                $provider = $config['default_provider'] ?? 'openai';
            }
            
            // Validate provider exists
            if (!isset($config[$provider])) {
                throw new \Exception("Provider '$provider' not found in configuration");
            }
            
            $providerConfig = $config[$provider];
            
            // Check if provider is enabled
            if (empty($providerConfig['enabled'])) {
                throw new \Exception("Provider '$provider' is not enabled. Please enable it in Administration > AI Integration.");
            }
            
            // Check if API key is configured
            if (empty($providerConfig['api_key'])) {
                throw new \Exception("API Key not configured for '$provider'. Please set it in Administration > AI Integration.");
            }
            
            error_log("AI Integration Query - Using provider: $provider");
            
            // Use AIProviderHelper for consistent API calls
            $response = AIProviderHelper::sendToAI(
                $provider,
                $providerConfig,
                $question,
                $context
            );
            
            if ($response['success']) {
                $result = [
                    'success' => true,
                    'response' => $response['message'],
                    'provider' => $provider
                ];
            } else {
                $result = [
                    'success' => false,
                    'error' => $response['error'] ?? 'Unknown error occurred'
                ];
            }
            
        } catch (\Exception $e) {
            error_log('AI Integration Query Error: ' . $e->getMessage());
            error_log('AI Integration Query Stack: ' . $e->getTraceAsString());
            
            $result = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
        
        // Send JSON response
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
