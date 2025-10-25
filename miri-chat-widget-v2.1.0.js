/**
 * Miri Chat Widget Pro - Client Side JavaScript
 * Version: 2.1.0
 * Enhanced with better error handling, session management, and UX improvements
 */

(function() {
    'use strict';
    
    // Configuration (will be injected from PHP)
    const config = window.miriChatConfig || {};
    
    // DOM Elements
    const elements = {
        wrap: null,
        launcher: null,
        feed: null,
        form: null,
        input: null,
        sendBtn: null
    };
    
    // State management
    const state = {
        isOpen: false,
        isTyping: false,
        sessionId: null,
        messageCount: 0,
        reconnectAttempts: 0,
        maxReconnectAttempts: 3
    };
    
    // Storage keys
    const STORAGE_KEYS = {
        SESSION: 'miri_session_id_v2',
        HISTORY: 'miri_chat_history_v2',
        LAST_SEEN: 'miri_last_seen'
    };
    
    /**
     * Initialize the chat widget
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Get DOM elements
        elements.wrap = document.getElementById('miri-wrap');
        elements.launcher = document.getElementById('miri-launch');
        elements.feed = document.getElementById('miri-feed');
        elements.form = document.getElementById('miri-form');
        elements.input = document.getElementById('miri-text');
        elements.sendBtn = elements.form ? elements.form.querySelector('.miri-send') : null;
        
        if (!elements.wrap || !elements.launcher) {
            console.error('Miri Chat: Required elements not found');
            return;
        }
        
        // Initialize session
        initSession();
        
        // Restore chat history
        restoreHistory();
        
        // Setup event listeners
        setupEventListeners();
        
        // Initialize visual effects
        initVisualEffects();
        
        console.log('Miri Chat Widget initialized successfully');
    }
    
    /**
     * Initialize or restore session
     */
    function initSession() {
        let sessionId = localStorage.getItem(STORAGE_KEYS.SESSION);
        
        if (!sessionId) {
            // Create new session ID
            sessionId = generateUUID();
            localStorage.setItem(STORAGE_KEYS.SESSION, sessionId);
        }
        
        state.sessionId = sessionId;
    }
    
    /**
     * Generate UUID v4
     */
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    
    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Launcher click
        elements.launcher.addEventListener('click', toggleChat);
        
        // Form submit
        if (elements.form) {
            elements.form.addEventListener('submit', handleSubmit);
        }
        
        // Input handlers
        if (elements.input) {
            elements.input.addEventListener('input', handleInputChange);
            elements.input.addEventListener('keydown', handleKeyDown);
        }
        
        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.isOpen) {
                toggleChat();
            }
        });
        
        // Handle visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && state.isOpen) {
                updateLastSeen();
            }
        });
    }
    
    /**
     * Toggle chat window
     */
    function toggleChat() {
        state.isOpen = !state.isOpen;
        elements.wrap.classList.toggle('open', state.isOpen);
        
        if (state.isOpen) {
            elements.input.focus();
            
            // Show welcome messages on first open
            if (state.messageCount === 0) {
                showWelcomeMessages();
            }
            
            // Mark as seen
            updateLastSeen();
            
            // Scroll to bottom
            setTimeout(scrollToBottom, 100);
        }
    }
    
    /**
     * Show welcome messages
     */
    function showWelcomeMessages() {
        if (config.welcome1) {
            addMessage('assistant', config.welcome1, null, false);
        }
        
        if (config.welcome2) {
            setTimeout(function() {
                addMessage('assistant', config.welcome2, null, false);
            }, 500);
        }
    }
    
    /**
     * Handle form submission
     */
    async function handleSubmit(e) {
        e.preventDefault();
        
        const text = elements.input.value.trim();
        if (!text) return;
        
        // Add user message
        addMessage('user', escapeHtml(text));
        
        // Clear input
        elements.input.value = '';
        elements.input.style.height = 'auto';
        
        // Disable send button
        elements.sendBtn.disabled = true;
        
        // Show typing indicator
        if (config.typingIndicator) {
            showTypingIndicator();
        }
        
        try {
            // Send message to webhook
            const response = await sendMessage(text);
            
            // Remove typing indicator
            removeTypingIndicator();
            
            // Process response
            const botText = extractBotResponse(response);
            
            if (botText) {
                addMessage('assistant', escapeHtml(botText));
                
                // Play sound notification if enabled
                if (config.soundEnabled) {
                    playNotificationSound();
                }
            } else {
                throw new Error('Empty response from server');
            }
            
            // Reset reconnect attempts on success
            state.reconnectAttempts = 0;
            
        } catch (error) {
            console.error('Miri Chat Error:', error);
            
            removeTypingIndicator();
            
            // Handle retry logic
            if (state.reconnectAttempts < state.maxReconnectAttempts) {
                state.reconnectAttempts++;
                addMessage('assistant', 'מתנצלת, הייתה בעיה. מנסה שוב...', null, false);
                
                setTimeout(function() {
                    handleSubmit({ preventDefault: function() {}, target: elements.form });
                }, 2000);
            } else {
                addMessage('assistant', 'מצטערת, יש בעיית חיבור. אנא נסו שוב מאוחר יותר או פנו אלינו בדרך אחרת.');
                state.reconnectAttempts = 0;
            }
        } finally {
            elements.sendBtn.disabled = false;
            scrollToBottom();
        }
    }
    
    /**
     * Send message to webhook
     */
    async function sendMessage(text) {
        const response = await fetch(config.webhook, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                sessionId: state.sessionId,
                chatInput: text,
                metadata: {
                    source: 'miri-chat-pro-v2',
                    url: window.location.href,
                    referrer: document.referrer,
                    timestamp: new Date().toISOString(),
                    userAgent: navigator.userAgent
                }
            })
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        
        return await response.json();
    }
    
    /**
     * Extract bot response from various response formats
     */
    function extractBotResponse(data) {
        if (!data) return '';
        
        // String response
        if (typeof data === 'string') return data;
        
        // Direct reply field
        if (data.reply) return data.reply;
        if (data.response) return data.response;
        if (data.answer) return data.answer;
        if (data.text) return data.text;
        
        // n8n style response
        if (data.output) return data.output;
        
        // Message array format
        if (Array.isArray(data.messages)) {
            const lastMessage = data.messages
                .slice()
                .reverse()
                .find(m => m.role !== 'user' && m.role !== 'human');
            
            if (lastMessage) {
                return lastMessage.text || lastMessage.content || lastMessage.message || '';
            }
        }
        
        // Nested data object
        if (data.data) {
            return extractBotResponse(data.data);
        }
        
        return '';
    }
    
    /**
     * Add message to chat
     */
    function addMessage(role, text, timestamp = null, persist = true) {
        if (!text) return;
        
        const ts = timestamp || new Date().toISOString();
        
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = 'msg' + (role === 'user' ? ' me' : '');
        
        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.innerHTML = text;
        
        const timeEl = document.createElement('div');
        timeEl.className = 'ts';
        timeEl.textContent = formatTime(ts);
        
        messageEl.appendChild(bubble);
        messageEl.appendChild(timeEl);
        
        elements.feed.appendChild(messageEl);
        
        // Increment message count
        state.messageCount++;
        
        // Persist to storage
        if (persist) {
            saveMessage(role, stripScripts(text), ts);
        }
        
        // Scroll to bottom
        scrollToBottom();
    }
    
    /**
     * Show typing indicator
     */
    function showTypingIndicator() {
        if (state.isTyping) return;
        
        state.isTyping = true;
        
        const loader = document.createElement('div');
        loader.className = 'msg';
        loader.dataset.loader = '1';
        
        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.innerHTML = '<div class="loader"><span></span><span></span><span></span></div>';
        
        const timeEl = document.createElement('div');
        timeEl.className = 'ts';
        
        loader.appendChild(bubble);
        loader.appendChild(timeEl);
        
        elements.feed.appendChild(loader);
        scrollToBottom();
    }
    
    /**
     * Remove typing indicator
     */
    function removeTypingIndicator() {
        const loader = elements.feed.querySelector('[data-loader="1"]');
        if (loader) {
            loader.remove();
        }
        state.isTyping = false;
    }
    
    /**
     * Handle input change
     */
    function handleInputChange() {
        // Auto-resize textarea
        elements.input.style.height = 'auto';
        elements.input.style.height = Math.min(elements.input.scrollHeight, 110) + 'px';
    }
    
    /**
     * Handle key down in input
     */
    function handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            elements.form.requestSubmit();
        }
    }
    
    /**
     * Restore chat history from localStorage
     */
    function restoreHistory() {
        try {
            const raw = localStorage.getItem(STORAGE_KEYS.HISTORY);
            if (!raw) return;
            
            const messages = JSON.parse(raw);
            const maxMessages = config.maxHistoryMessages || 50;
            
            // Only restore recent messages
            const recentMessages = messages.slice(-maxMessages);
            
            recentMessages.forEach(function(msg) {
                if (msg.role && msg.text) {
                    addMessage(msg.role, msg.text, msg.ts, false);
                }
            });
            
            scrollToBottom();
        } catch (error) {
            console.error('Error restoring history:', error);
            localStorage.removeItem(STORAGE_KEYS.HISTORY);
        }
    }
    
    /**
     * Save message to localStorage
     */
    function saveMessage(role, text, timestamp) {
        try {
            const raw = localStorage.getItem(STORAGE_KEYS.HISTORY);
            const messages = raw ? JSON.parse(raw) : [];
            
            messages.push({
                role: role,
                text: text,
                ts: timestamp
            });
            
            // Keep only recent messages
            const maxMessages = config.maxHistoryMessages || 50;
            const trimmed = messages.slice(-maxMessages);
            
            localStorage.setItem(STORAGE_KEYS.HISTORY, JSON.stringify(trimmed));
        } catch (error) {
            console.error('Error saving message:', error);
        }
    }
    
    /**
     * Update last seen timestamp
     */
    function updateLastSeen() {
        localStorage.setItem(STORAGE_KEYS.LAST_SEEN, new Date().toISOString());
    }
    
    /**
     * Scroll chat to bottom
     */
    function scrollToBottom() {
        if (!elements.feed) return;
        
        elements.feed.scrollTo({
            top: elements.feed.scrollHeight,
            behavior: 'smooth'
        });
    }
    
    /**
     * Format timestamp
     */
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('he-IL', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    /**
     * Strip script tags for security
     */
    function stripScripts(text) {
        return String(text).replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
    }
    
    /**
     * Play notification sound
     */
    function playNotificationSound() {
        try {
            // Create a simple beep sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (error) {
            // Fail silently if audio not supported
        }
    }
    
    /**
     * Initialize visual effects (neural network background)
     */
    function initVisualEffects() {
        const canvas = document.getElementById('miri-net');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        let dpr = Math.max(1, window.devicePixelRatio || 1);
        let nodes = [];
        let animationId = null;
        
        function resize() {
            const rect = elements.wrap.getBoundingClientRect();
            const w = rect.width;
            const h = rect.height;
            
            canvas.width = Math.max(1, w) * dpr;
            canvas.height = Math.max(1, h) * dpr;
            canvas.style.width = w + 'px';
            canvas.style.height = h + 'px';
            
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            
            if (nodes.length === 0) {
                const count = Math.min(60, Math.floor((w * h) / 6000));
                nodes = Array.from({ length: count }, function() {
                    return {
                        x: Math.random() * w,
                        y: Math.random() * h,
                        vx: (Math.random() - 0.5) * 0.6,
                        vy: (Math.random() - 0.5) * 0.6
                    };
                });
            }
        }
        
        function animate() {
            const w = canvas.clientWidth;
            const h = canvas.clientHeight;
            
            ctx.clearRect(0, 0, w, h);
            
            // Update node positions
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                node.x += node.vx;
                node.y += node.vy;
                
                if (node.x < 0 || node.x > w) node.vx *= -1;
                if (node.y < 0 || node.y > h) node.vy *= -1;
            }
            
            // Draw connections
            ctx.lineWidth = 1;
            ctx.strokeStyle = 'rgba(200,220,255,0.22)';
            
            const maxDist = 130 * 130;
            
            for (let i = 0; i < nodes.length; i++) {
                const a = nodes[i];
                for (let j = i + 1; j < nodes.length; j++) {
                    const b = nodes[j];
                    const dx = a.x - b.x;
                    const dy = a.y - b.y;
                    const dist = dx * dx + dy * dy;
                    
                    if (dist < maxDist) {
                        ctx.globalAlpha = (1 - dist / maxDist) * 0.6;
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                }
            }
            
            // Draw nodes
            ctx.globalAlpha = 1;
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                ctx.beginPath();
                ctx.arc(node.x, node.y, 1.6, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,0.6)';
                ctx.fill();
            }
            
            animationId = requestAnimationFrame(animate);
        }
        
        // Setup resize observer
        const resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(elements.wrap);
        
        resize();
        animate();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (animationId) {
                cancelAnimationFrame(animationId);
            }
            resizeObserver.disconnect();
        });
    }
    
    // Initialize when ready
    init();
    
    // Expose API for external use
    window.MiriChat = {
        open: function() {
            if (!state.isOpen) toggleChat();
        },
        close: function() {
            if (state.isOpen) toggleChat();
        },
        sendMessage: function(text) {
            if (text) {
                elements.input.value = text;
                elements.form.requestSubmit();
            }
        },
        clearHistory: function() {
            localStorage.removeItem(STORAGE_KEYS.HISTORY);
            elements.feed.innerHTML = '';
            state.messageCount = 0;
            showWelcomeMessages();
        }
    };
    
})();
