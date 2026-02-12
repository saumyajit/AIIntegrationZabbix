<?php
/**
 * AI Integration Configuration View
 * Displays provider settings and quick action toggles
 */

$config = $data['config'] ?? [];
$message = $data['message'] ?? null;
$message_type = $data['message_type'] ?? 'success';

// Provider list
$providers = [
    'openai' => 'OpenAI',
    'github' => 'GitHub Models',
    'anthropic' => 'Anthropic Claude',
    'gemini' => 'Google Gemini',
    'deepseek' => 'DeepSeek',
    'mistral' => 'Mistral AI',
    'groq' => 'Groq',
    'custom' => 'Custom Provider'
];

?>

<style>
.ai-config-container {
    max-width: 1200px;
    margin: 20px auto;
}

.ai-config-section {
    background: white;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.ai-config-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.ai-provider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
}

.ai-provider-card {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 16px;
    background: #f9fafb;
}

.ai-provider-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.ai-provider-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.ai-field {
    margin-bottom: 12px;
}

.ai-field label {
    display: block;
    font-weight: 500;
    margin-bottom: 4px;
    color: #374151;
    font-size: 14px;
}

.ai-field input[type="text"],
.ai-field input[type="password"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
}

.ai-field-inline {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.ai-toggle {
    display: flex;
    align-items: center;
}

.ai-toggle input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 8px;
}

.ai-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.ai-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.ai-btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.ai-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.ai-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

.ai-message {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.ai-message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.ai-message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.ai-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}
</style>

<div class="ai-config-container">
    <?php if ($message): ?>
    <div class="ai-message <?= $message_type ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form id="ai-config-form" method="post">
        
        <!-- Provider Configuration -->
        <div class="ai-config-section">
            <h3>🤖 AI Provider Configuration</h3>
            
            <div class="ai-provider-grid">
                <?php foreach ($providers as $key => $name): 
                    $provider_config = $config[$key] ?? [];
                ?>
                <div class="ai-provider-card">
                    <div class="ai-provider-header">
                        <h4><?= htmlspecialchars($name) ?></h4>
                        <label class="ai-toggle">
                            <input type="checkbox" 
                                   name="<?= $key ?>_enabled" 
                                   value="1"
                                   <?= !empty($provider_config['enabled']) ? 'checked' : '' ?>>
                            <span>Enabled</span>
                        </label>
                    </div>
                    
                    <div class="ai-field">
                        <label>API Endpoint</label>
                        <input type="text" 
                               name="<?= $key ?>_api_endpoint" 
                               value="<?= htmlspecialchars($provider_config['api_endpoint'] ?? '') ?>"
                               placeholder="https://api.example.com/v1">
                    </div>
                    
                    <div class="ai-field">
                        <label>API Key</label>
                        <input type="password" 
                               name="<?= $key ?>_api_key" 
                               value="<?= !empty($provider_config['api_key']) ? '********' : '' ?>"
                               placeholder="Enter API key">
                    </div>
                    
                    <div class="ai-field">
                        <label>Default Model</label>
                        <input type="text" 
                               name="<?= $key ?>_default_model" 
                               value="<?= htmlspecialchars($provider_config['default_model'] ?? '') ?>"
                               placeholder="model-name">
                    </div>
                    
                    <div class="ai-field-inline">
                        <div class="ai-field">
                            <label>Temperature</label>
                            <input type="text" 
                                   name="<?= $key ?>_temperature" 
                                   value="<?= htmlspecialchars($provider_config['temperature'] ?? '0.7') ?>">
                        </div>
                        <div class="ai-field">
                            <label>Max Tokens</label>
                            <input type="text" 
                                   name="<?= $key ?>_max_tokens" 
                                   value="<?= htmlspecialchars($provider_config['max_tokens'] ?? '1000') ?>">
                        </div>
                    </div>
                    
                    <button type="button" 
                            class="ai-btn ai-btn-secondary ai-test-btn" 
                            data-provider="<?= $key ?>"
                            style="margin-top: 8px; width: 100%;">
                        🧪 Test Connection
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Default Provider -->
        <div class="ai-config-section">
            <h3>⚙️ Default Settings</h3>
            
            <div class="ai-field">
                <label>Default Provider</label>
                <select name="default_provider" style="width: 300px; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                    <?php foreach ($providers as $key => $name): ?>
                    <option value="<?= $key ?>" <?= ($config['default_provider'] ?? 'openai') === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="ai-config-section">
            <h3>⚡ Quick Actions</h3>
            <p style="color: #6b7280; margin-bottom: 16px;">Enable AI assistance buttons on different pages</p>
            
            <div class="ai-quick-actions">
                <label class="ai-toggle">
                    <input type="checkbox" 
                           name="qa_problems" 
                           value="1"
                           <?= !empty($config['quick_actions']['problems']) ? 'checked' : '' ?>>
                    <span>Problems Page</span>
                </label>
                
                <label class="ai-toggle">
                    <input type="checkbox" 
                           name="qa_triggers" 
                           value="1"
                           <?= !empty($config['quick_actions']['triggers']) ? 'checked' : '' ?>>
                    <span>Trigger Forms</span>
                </label>
                
                <label class="ai-toggle">
                    <input type="checkbox" 
                           name="qa_items" 
                           value="1"
                           <?= !empty($config['quick_actions']['items']) ? 'checked' : '' ?>>
                    <span>Latest Data Page</span>
                </label>
                
                <label class="ai-toggle">
                    <input type="checkbox" 
                           name="qa_hosts" 
                           value="1"
                           <?= !empty($config['quick_actions']['hosts']) ? 'checked' : '' ?>>
                    <span>Host Forms</span>
                </label>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="ai-actions">
            <button type="button" class="ai-btn ai-btn-secondary" onclick="window.location.reload()">
                Cancel
            </button>
            <button type="submit" name="save" class="ai-btn ai-btn-primary">
                💾 Save Configuration
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ai-config-form');
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        formData.append('save', '1');
        
        const saveBtn = form.querySelector('button[name="save"]');
        saveBtn.disabled = true;
        saveBtn.textContent = '⏳ Saving...';
        
        fetch('zabbix.php?action=aiintegration.config', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Configuration saved successfully!', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showMessage(data.message || 'Failed to save configuration', 'error');
                saveBtn.disabled = false;
                saveBtn.textContent = '💾 Save Configuration';
            }
        })
        .catch(error => {
            showMessage('Error: ' + error.message, 'error');
            saveBtn.disabled = false;
            saveBtn.textContent = '💾 Save Configuration';
        });
    });
    
    // Handle test buttons
    document.querySelectorAll('.ai-test-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const provider = this.dataset.provider;
            testConnection(provider, this);
        });
    });
    
    function testConnection(provider, btn) {
        const endpoint = document.querySelector(`input[name="${provider}_api_endpoint"]`).value;
        const apiKey = document.querySelector(`input[name="${provider}_api_key"]`).value;
        
        if (!endpoint || !apiKey || apiKey === '********') {
            alert('Please enter both API Endpoint and API Key');
            return;
        }
        
        btn.disabled = true;
        btn.textContent = '⏳ Testing...';
        
        const formData = new FormData();
        formData.append('test', '1');
        formData.append('provider', provider);
        formData.append('api_endpoint', endpoint);
        formData.append('api_key', apiKey);
        
        fetch('zabbix.php?action=aiintegration.config', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Connection successful!', 'success');
            } else {
                showMessage(data.message || 'Connection failed', 'error');
            }
            btn.disabled = false;
            btn.textContent = '🧪 Test Connection';
        })
        .catch(error => {
            showMessage('Test error: ' + error.message, 'error');
            btn.disabled = false;
            btn.textContent = '🧪 Test Connection';
        });
    }
    
    function showMessage(text, type) {
        // Remove existing messages
        document.querySelectorAll('.ai-message').forEach(m => m.remove());
        
        const msg = document.createElement('div');
        msg.className = 'ai-message ' + type;
        msg.textContent = text;
        
        const container = document.querySelector('.ai-config-container');
        container.insertBefore(msg, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => msg.remove(), 5000);
    }
});
</script>
