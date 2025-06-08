/**
 * Link Shortener JavaScript Functions
 */

// Bot detection function - basic version
function detectBot() {
    // Simple bot detection - fill a hidden field
    // Most bots will fill all fields, humans won't see this field
    document.addEventListener('DOMContentLoaded', function() {
        const botCheckField = document.getElementById('bot_check');
        if (botCheckField) {
            // If this field gets a value, it's likely a bot
            if (botCheckField.value !== '') {
                // Redirect to homepage or show error
                window.location.href = '/';
            }
        }
    });
}

// Advanced bot detection
function detectAdvancedBot() {
    // Check for common bot behaviors
    const botPatterns = [
        navigator.webdriver,
        window.document.documentElement.getAttribute("webdriver"),
        navigator.userAgent.toLowerCase().indexOf("headless") > -1,
        navigator.languages === "",
        navigator.plugins.length === 0
    ];
    
    const botCheckField = document.getElementById('bot_check');
    if (botCheckField && botPatterns.some(pattern => pattern === true)) {
        botCheckField.value = 'detected';
        return true;
    }
    
    // Monitor mouse movements (bots often don't move mouse)
    let mouseMovements = 0;
    document.addEventListener('mousemove', function() {
        mouseMovements++;
    });
    
    // After 5 seconds, check mouse movements
    setTimeout(function() {
        if (mouseMovements < 5 && botCheckField) {
            botCheckField.value = 'detected';
        }
    }, 5000);
    
    return false;
}

// Copy link to clipboard function
function copyToClipboard(text) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    // Show a success message
    const message = document.createElement('div');
    message.className = 'copy-success';
    message.textContent = 'Link copied to clipboard!';
    document.body.appendChild(message);
    
    // Remove the message after 2 seconds
    setTimeout(() => {
        document.body.removeChild(message);
    }, 2000);
}

// Share functionality
function shareLink(platform) {
    const shareUrl = window.location.href;
    const shareTitle = document.title;
    
    switch(platform) {
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
            break;
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareTitle)}`, '_blank');
            break;
        case 'whatsapp':
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(shareTitle + ' ' + shareUrl)}`, '_blank');
            break;
    }
}

// Request browser notification permission
function requestNotificationPermission() {
    if ('Notification' in window) {
        if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    new Notification('Notification Enabled', {
                        body: 'Thank you for enabling notifications. You will be notified when verification is complete.'
                    });
                }
            });
        }
    }
}

// Display notification when verification complete
function showCompletionNotification() {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Verification Complete', {
            body: 'Your security verification is complete. You can now proceed to your destination.'
        });
    }
}

// Scanning animation with dots
function animateScanningDots() {
    const dotsElement = document.getElementById('scanning-dots');
    if (dotsElement) {
        let dotCount = 0;
        
        setInterval(function() {
            dotCount = (dotCount % 3) + 1;
            let dots = '';
            for (let i = 0; i < dotCount; i++) {
                dots += '.';
            }
            dotsElement.textContent = dots;
        }, 500);
    }
}

// PHP Fallback Timer via Cookie
function setFallbackTimer() {
    const now = new Date();
    const expiry = new Date(now.getTime() + 15000); // 15 seconds
    document.cookie = `timer_expiry=${expiry.getTime()};path=/`;
}

// Check if timer should be completed based on cookie
function checkFallbackTimer() {
    const cookies = document.cookie.split(';');
    for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'timer_expiry') {
            const expiry = parseInt(value);
            const now = new Date().getTime();
            if (now >= expiry) {
                // Enable continue button if timer has expired
                const continueBtn = document.getElementById('continue-btn');
                if (continueBtn) {
                    continueBtn.disabled = false;
                }
                return true;
            }
        }
    }
    return false;
}

// Analytics tracking (basic)
function trackEvent(eventName, eventData = {}) {
    // Simple analytics tracking - store in localStorage for now
    // In a real implementation, this would send data to a server
    try {
        const analytics = JSON.parse(localStorage.getItem('link_analytics') || '{}');
        const timestamp = new Date().toISOString();
        
        if (!analytics[eventName]) {
            analytics[eventName] = [];
        }
        
        analytics[eventName].push({
            timestamp,
            ...eventData
        });
        
        localStorage.setItem('link_analytics', JSON.stringify(analytics));
    } catch (e) {
        console.error('Analytics tracking failed', e);
    }
}

// Initialize functions
document.addEventListener('DOMContentLoaded', function() {
    // Run bot detection
    detectBot();
    detectAdvancedBot();
    
    // Set fallback timer
    setFallbackTimer();
    setTimeout(checkFallbackTimer, 15000);
    
    // Add copy functionality to copy buttons
    const copyButtons = document.querySelectorAll('.copy-btn');
    if (copyButtons.length > 0) {
        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const link = this.getAttribute('data-link');
                copyToClipboard(link);
                trackEvent('link_copied', { link });
            });
        });
    }
    
    // Track page view
    trackEvent('page_view', { 
        url: window.location.href,
        referrer: document.referrer
    });
    
    // Request notification permission after a delay
    setTimeout(requestNotificationPermission, 5000);
    
    // Animate scanning dots if element exists
    animateScanningDots();
}); 