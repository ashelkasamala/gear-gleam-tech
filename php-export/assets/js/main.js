/**
 * AUTO SPARE WORKSHOP - Main JavaScript
 * Premium Auto Parts & Services Platform
 */

// ============================================
// THEME TOGGLE
// ============================================

const ThemeManager = {
    init() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);
        
        // Listen for toggle clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.theme-toggle')) {
                this.toggle();
            }
        });
    },
    
    setTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem('theme', theme);
        this.updateToggleIcon(theme);
    },
    
    toggle() {
        const current = localStorage.getItem('theme') || 'light';
        this.setTheme(current === 'light' ? 'dark' : 'light');
    },
    
    updateToggleIcon(theme) {
        const toggles = document.querySelectorAll('.theme-toggle');
        toggles.forEach(toggle => {
            const icon = toggle.querySelector('svg');
            if (icon) {
                icon.innerHTML = theme === 'light' 
                    ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>'
                    : '<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>';
            }
        });
    }
};

// ============================================
// MOBILE MENU
// ============================================

const MobileMenu = {
    init() {
        const toggle = document.querySelector('.mobile-menu-toggle');
        const menu = document.querySelector('.mobile-menu');
        
        if (toggle && menu) {
            toggle.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                menu.classList.toggle('animate-fade-in');
            });
        }
    }
};

// ============================================
// SEARCH
// ============================================

const Search = {
    debounceTimeout: null,
    
    init() {
        const searchInputs = document.querySelectorAll('.search-input input');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => this.handleInput(e.target.value));
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    this.search(e.target.value);
                }
            });
        });
    },
    
    handleInput(value) {
        clearTimeout(this.debounceTimeout);
        
        if (value.length >= 2) {
            this.debounceTimeout = setTimeout(() => {
                this.fetchSuggestions(value);
            }, 300);
        } else {
            this.hideSuggestions();
        }
    },
    
    async fetchSuggestions(query) {
        try {
            const response = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            this.showSuggestions(data);
        } catch (error) {
            console.error('Search error:', error);
        }
    },
    
    showSuggestions(suggestions) {
        // Implement dropdown suggestions UI
        console.log('Suggestions:', suggestions);
    },
    
    hideSuggestions() {
        // Hide suggestions dropdown
    },
    
    search(query) {
        if (query.trim()) {
            window.location.href = `/shop.php?search=${encodeURIComponent(query)}`;
        }
    }
};

// ============================================
// CART
// ============================================

const Cart = {
    init() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart')) {
                e.preventDefault();
                const btn = e.target.closest('.add-to-cart');
                const productId = btn.dataset.productId;
                const quantity = btn.dataset.quantity || 1;
                this.add(productId, quantity);
            }
            
            if (e.target.closest('.remove-from-cart')) {
                e.preventDefault();
                const btn = e.target.closest('.remove-from-cart');
                const productId = btn.dataset.productId;
                this.remove(productId);
            }
            
            if (e.target.closest('.update-quantity')) {
                const btn = e.target.closest('.update-quantity');
                const productId = btn.dataset.productId;
                const quantity = btn.dataset.quantity;
                this.update(productId, quantity);
            }
        });
    },
    
    async add(productId, quantity) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                Toast.show('success', 'Added to cart!');
            } else {
                Toast.show('error', data.message);
            }
        } catch (error) {
            Toast.show('error', 'Failed to add to cart');
        }
    },
    
    async update(productId, quantity) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update',
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.updateCartTotals(data.totals);
            }
        } catch (error) {
            Toast.show('error', 'Failed to update cart');
        }
    },
    
    async remove(productId) {
        try {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount(data.cart_count);
                // Remove row from DOM
                const row = document.querySelector(`[data-cart-item="${productId}"]`);
                if (row) row.remove();
                Toast.show('success', 'Item removed');
            }
        } catch (error) {
            Toast.show('error', 'Failed to remove item');
        }
    },
    
    updateCartCount(count) {
        const badges = document.querySelectorAll('.cart-count');
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    },
    
    updateCartTotals(totals) {
        if (totals) {
            const subtotal = document.querySelector('.cart-subtotal');
            const tax = document.querySelector('.cart-tax');
            const shipping = document.querySelector('.cart-shipping');
            const total = document.querySelector('.cart-total');
            
            if (subtotal) subtotal.textContent = `$${totals.subtotal.toFixed(2)}`;
            if (tax) tax.textContent = `$${totals.tax.toFixed(2)}`;
            if (shipping) shipping.textContent = totals.shipping > 0 ? `$${totals.shipping.toFixed(2)}` : 'FREE';
            if (total) total.textContent = `$${totals.total.toFixed(2)}`;
        }
    }
};

// ============================================
// CHAT WIDGET
// ============================================

const ChatWidget = {
    isOpen: false,
    conversationId: null,
    pollInterval: null,
    
    init() {
        const toggle = document.querySelector('.chat-toggle');
        const widget = document.querySelector('.chat-widget');
        const closeBtn = document.querySelector('.chat-close');
        const minimizeBtn = document.querySelector('.chat-minimize');
        const sendBtn = document.querySelector('.chat-send');
        const input = document.querySelector('.chat-input');
        
        if (toggle) {
            toggle.addEventListener('click', () => this.open());
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
        
        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', () => this.minimize());
        }
        
        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.send());
        }
        
        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.send();
                }
            });
        }
    },
    
    open() {
        const widget = document.querySelector('.chat-widget');
        const toggle = document.querySelector('.chat-toggle');
        
        if (widget && toggle) {
            widget.style.display = 'flex';
            toggle.style.display = 'none';
            this.isOpen = true;
            this.startPolling();
        }
    },
    
    close() {
        const widget = document.querySelector('.chat-widget');
        const toggle = document.querySelector('.chat-toggle');
        
        if (widget && toggle) {
            widget.style.display = 'none';
            toggle.style.display = 'flex';
            this.isOpen = false;
            this.stopPolling();
        }
    },
    
    minimize() {
        const widget = document.querySelector('.chat-widget');
        widget.classList.toggle('minimized');
    },
    
    async send() {
        const input = document.querySelector('.chat-input');
        const message = input.value.trim();
        
        if (!message) return;
        
        try {
            const response = await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send',
                    conversation_id: this.conversationId,
                    message: message
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.conversationId = data.conversation_id;
                input.value = '';
                this.appendMessage(data.message, true);
            }
        } catch (error) {
            console.error('Chat error:', error);
        }
    },
    
    appendMessage(message, isUser) {
        const container = document.querySelector('.chat-messages');
        const html = `
            <div class="chat-message ${isUser ? 'user' : ''}">
                <div class="chat-bubble ${isUser ? 'user' : 'admin'}">
                    <p>${message.content}</p>
                    <span class="chat-time">${new Date(message.created_at).toLocaleTimeString()}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;
    },
    
    startPolling() {
        this.pollInterval = setInterval(() => this.fetchMessages(), 3000);
    },
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
    },
    
    async fetchMessages() {
        if (!this.conversationId) return;
        
        try {
            const response = await fetch(`/api/chat.php?action=messages&conversation_id=${this.conversationId}`);
            const data = await response.json();
            
            if (data.success && data.messages) {
                // Update messages container
            }
        } catch (error) {
            console.error('Chat fetch error:', error);
        }
    }
};

// ============================================
// TOAST NOTIFICATIONS
// ============================================

const Toast = {
    show(type, message, duration = 3000) {
        const container = document.querySelector('.toast-container') || this.createContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} animate-fade-in`;
        toast.innerHTML = `
            <span class="toast-icon">${this.getIcon(type)}</span>
            <span class="toast-message">${message}</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    
    createContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        container.style.cssText = 'position: fixed; top: 1rem; right: 1rem; z-index: 100; display: flex; flex-direction: column; gap: 0.5rem;';
        document.body.appendChild(container);
        return container;
    },
    
    getIcon(type) {
        switch (type) {
            case 'success': return '✓';
            case 'error': return '✕';
            case 'warning': return '⚠';
            default: return 'ℹ';
        }
    }
};

// ============================================
// FORM VALIDATION
// ============================================

const FormValidation = {
    init() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validate(form)) {
                    e.preventDefault();
                }
            });
        });
    },
    
    validate(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        inputs.forEach(input => {
            this.clearError(input);
            
            if (!input.value.trim()) {
                this.showError(input, 'This field is required');
                isValid = false;
            } else if (input.type === 'email' && !this.isValidEmail(input.value)) {
                this.showError(input, 'Please enter a valid email');
                isValid = false;
            }
        });
        
        // Password confirmation
        const password = form.querySelector('input[name="password"]');
        const confirm = form.querySelector('input[name="confirm_password"]');
        if (password && confirm && password.value !== confirm.value) {
            this.showError(confirm, 'Passwords do not match');
            isValid = false;
        }
        
        return isValid;
    },
    
    showError(input, message) {
        input.classList.add('input-error');
        const error = document.createElement('span');
        error.className = 'error-message';
        error.textContent = message;
        error.style.cssText = 'color: var(--destructive); font-size: 0.75rem; margin-top: 0.25rem; display: block;';
        input.parentNode.appendChild(error);
    },
    
    clearError(input) {
        input.classList.remove('input-error');
        const error = input.parentNode.querySelector('.error-message');
        if (error) error.remove();
    },
    
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
};

// ============================================
// PRODUCT FILTERS
// ============================================

const ProductFilters = {
    init() {
        const filterForm = document.querySelector('.filter-form');
        
        if (filterForm) {
            filterForm.addEventListener('change', () => this.applyFilters());
        }
        
        // Price range slider
        const priceSlider = document.querySelector('.price-slider');
        if (priceSlider) {
            priceSlider.addEventListener('input', (e) => {
                document.querySelector('.price-value').textContent = `$${e.target.value}`;
            });
        }
    },
    
    applyFilters() {
        const form = document.querySelector('.filter-form');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        window.location.href = `/shop.php?${params.toString()}`;
    },
    
    clearFilters() {
        window.location.href = '/shop.php';
    }
};

// ============================================
// QUANTITY SELECTOR
// ============================================

const QuantitySelector = {
    init() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.qty-decrease')) {
                this.change(e.target.closest('.quantity-selector'), -1);
            }
            if (e.target.closest('.qty-increase')) {
                this.change(e.target.closest('.quantity-selector'), 1);
            }
        });
    },
    
    change(selector, delta) {
        const input = selector.querySelector('.qty-input');
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 999;
        let value = parseInt(input.value) + delta;
        
        value = Math.max(min, Math.min(max, value));
        input.value = value;
        
        // Trigger change event
        input.dispatchEvent(new Event('change'));
    }
};

// ============================================
// INITIALIZE ON DOM READY
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    MobileMenu.init();
    Search.init();
    Cart.init();
    ChatWidget.init();
    FormValidation.init();
    ProductFilters.init();
    QuantitySelector.init();
    
    // Animation on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
});
