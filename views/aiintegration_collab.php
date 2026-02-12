<?php
/**
 * AI Collaboration Chat View
 * Interactive chat interface with AI
 */

$config = $data['config'] ?? [];
$chat_history = $data['chat_history'] ?? [];
$enabled_providers = [];

// Get list of enabled providers
$provider_names = [
    'openai' => 'OpenAI',
    'github' => 'GitHub Models',
    'anthropic' => 'Anthropic Claude',
    'gemini' => 'Google Gemini',
    'deepseek' => 'DeepSeek',
    'mistral' => 'Mistral AI',
    'groq' => 'Groq',
    'custom' => 'Custom'
];

foreach ($provider_names as $key => $name) {
    if (!empty($config[$key]['enabled']) && !empty($config[$key]['api_key'])) {
        $enabled_providers[$key] = $name;
    }
}

$default_provider = $config['default_provider'] ?? 'openai';

?>

<style>
.ai-chat-container {
    max-width: 1000px;
    margin: 20px auto;
    height: calc(100vh - 200px);
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.ai-chat-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ai-chat-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.ai-chat-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ai-chat-controls select {
    padding: 6px 12px;
    border-radius: 4px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.9);
    font-size: 14px;
}

.ai-chat-controls button {
    padding: 6px 16px;
    border-radius: 4px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.2);
    color: white;
    cursor: pointer;
    font-size: 14px;
}

.ai-chat-controls button:hover {
    background: rgba(255,255,255,0.3);
}

.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f9fafb;
}

.ai-chat-message {
    margin-bottom: 16px;
    display: flex;
    gap: 12px;
}

.ai-chat-message.user {
    justify-content: flex-end;
}

.ai-chat-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.ai-chat-message.user .ai-chat-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.ai-chat-message.assistant .ai-chat-avatar {
    background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
}

.ai-chat-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 12px;
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.5;
}

.ai-chat-message.user .ai-chat-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.ai-chat-message.assistant .ai-chat-bubble {
    background: white;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}

.ai-chat-input-area {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    background: white;
}

.ai-chat-input-form {
    display: flex;
    gap: 12px;
}

.ai-chat-input-form textarea {
    flex: 1;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    resize: none;
    font-family: inherit;
    font-size: 14px;
    min-height: 50px;
    max-height: 120px;
}

.ai-chat-input-form textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.ai-chat-send-btn {
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.ai-chat-send-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.ai-chat-send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.ai-chat-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #9ca3af;
}

.ai-chat-empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
}

.ai-chat-empty-text {
    font-size: 18px;
    margin-bottom: 8px;
}

.ai-chat-empty-subtext {
    font-size: 14px;
    color: #d1d5db;
}

.ai-chat-loading {
    display: flex;
    gap: 4px;
    padding: 12px;
}

.ai-chat-loading-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #9ca3af;
    animation: bounce 1.4s infinite ease-in-out both;
}

.ai-chat-loading-dot:nth-child(1) {
    animation-delay: -0.32s;
}

.ai-chat-loading-dot:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes bounce {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}
</style>

<div class="ai-chat-container">
    <div class="ai-chat-header">
        <h2>🤖 AI Collaboration</h2>
        <div class="ai-chat-controls">
            <select id="provider-select">
                <?php foreach ($enabled_providers as $key => $name): ?>
                <option value="<?= $key ?>" <?= $key === $default_provider ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button id="clear-chat-btn">🗑️ Clear History</button>
        </div>
    </div>
    
    <div class="ai-chat-messages" id="chat-messages">
        <?php if (empty($chat_history)): ?>
        <div class="ai-chat-empty">
            <div class="ai-chat-empty-icon">💬</div>
            <div class="ai-chat-empty-text">Start a conversation</div>
            <div class="ai-chat-empty-subtext">Ask me anything about your Zabbix monitoring</div>
        </div>
        <?php else: ?>
            <?php foreach ($chat_history as $msg): ?>
            <div class="ai-chat-message <?= htmlspecialchars($msg['role']) ?>">
                <?php if ($msg['role'] === 'user'): ?>
                    <div class="ai-chat-bubble"><?= htmlspecialchars($msg['content']) ?></div>
                    <div class="ai-chat-avatar">👤</div>
                <?php else: ?>
                    <div class="ai-chat-avatar">🤖</div>
                    <div class="ai-chat-bubble"><?= htmlspecialchars($msg['content']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="ai-chat-input-area">
        <form class="ai-chat-input-form" id="chat-form">
            <textarea 
                id="message-input" 
                placeholder="Type your message here..."
                rows="2"
                required
            ></textarea>
            <button type="submit" class="ai-chat-send-btn" id="send-btn">
                ✉️ Send
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const providerSelect = document.getElementById('provider-select');
    const clearBtn = document.getElementById('clear-chat-btn');
    
    // Auto-scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Add message to chat
    function addMessage(role, content) {
        // Remove empty state if present
        const emptyState = chatMessages.querySelector('.ai-chat-empty');
        if (emptyState) {
            emptyState.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'ai-chat-message ' + role;
        
        if (role === 'user') {
            messageDiv.innerHTML = `
                <div class="ai-chat-bubble">${escapeHtml(content)}</div>
                <div class="ai-chat-avatar">👤</div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="ai-chat-avatar">🤖</div>
                <div class="ai-chat-bubble">${escapeHtml(content)}</div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }
    
    // Add loading indicator
    function addLoading() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'ai-chat-message assistant';
        loadingDiv.id = 'loading-indicator';
        loadingDiv.innerHTML = `
            <div class="ai-chat-avatar">🤖</div>
            <div class="ai-chat-bubble">
                <div class="ai-chat-loading">
                    <div class="ai-chat-loading-dot"></div>
                    <div class="ai-chat-loading-dot"></div>
                    <div class="ai-chat-loading-dot"></div>
                </div>
            </div>
        `;
        chatMessages.appendChild(loadingDiv);
        scrollToBottom();
    }
    
    function removeLoading() {
        const loading = document.getElementById('loading-indicator');
        if (loading) {
            loading.remove();
        }
    }
    
    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        const provider = providerSelect.value;
        
        // Add user message
        addMessage('user', message);
        messageInput.value = '';
        
        // Disable input
        sendBtn.disabled = true;
        messageInput.disabled = true;
        sendBtn.textContent = '⏳ Sending...';
        
        // Add loading
        addLoading();
        
        // Send to server
        const formData = new FormData();
        formData.append('send', '1');
        formData.append('message', message);
        formData.append('provider', provider);
        
        fetch('zabbix.php?action=aiintegration.collab', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            removeLoading();
            
            if (data.success) {
                addMessage('assistant', data.message);
            } else {
                addMessage('assistant', '❌ Error: ' + (data.error || 'Failed to get response'));
            }
            
            // Re-enable input
            sendBtn.disabled = false;
            messageInput.disabled = false;
            sendBtn.textContent = '✉️ Send';
            messageInput.focus();
        })
        .catch(error => {
            removeLoading();
            addMessage('assistant', '❌ Network error: ' + error.message);
            
            sendBtn.disabled = false;
            messageInput.disabled = false;
            sendBtn.textContent = '✉️ Send';
        });
    });
    
    // Handle Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Clear chat history
    clearBtn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to clear the chat history?')) {
            return;
        }
        
        fetch('zabbix.php?action=aiintegration.collab', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'clear=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            alert('Failed to clear history: ' + error.message);
        });
    });
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initial scroll
    scrollToBottom();
});
</script>
