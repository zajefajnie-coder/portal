// StageOne Modeling Portal JavaScript

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initLightbox();
    initRatingSystem();
    initFollowButtons();
    initLikeButtons();
    initDragAndDrop();
    initNotifications();
    initOnlineStatus();
    initSearchFunctionality();
});

// Lightbox functionality
function initLightbox() {
    const imageItems = document.querySelectorAll('.image-item');
    
    imageItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                const imgSrc = e.target.src;
                const alt = e.target.alt;
                
                // Create lightbox HTML
                const lightbox = document.createElement('div');
                lightbox.className = 'lightbox active';
                lightbox.innerHTML = `
                    <div class="lightbox-content">
                        <img src="${imgSrc}" alt="${alt}">
                    </div>
                    <button class="lightbox-close">&times;</button>
                    <div class="lightbox-nav">
                        <button class="prev-btn">&#8249;</button>
                        <button class="next-btn">&#8250;</button>
                    </div>
                `;
                
                document.body.appendChild(lightbox);
                document.body.style.overflow = 'hidden';
                
                // Close lightbox
                lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
                lightbox.addEventListener('click', function(e) {
                    if (e.target === lightbox) {
                        closeLightbox();
                    }
                });
                
                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeLightbox();
                    }
                });
            }
        });
    });
    
    function closeLightbox() {
        const lightbox = document.querySelector('.lightbox');
        if (lightbox) {
            lightbox.classList.remove('active');
            setTimeout(() => {
                document.body.removeChild(lightbox);
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }
}

// Rating system
function initRatingSystem() {
    const ratingElements = document.querySelectorAll('.rating-stars');
    
    ratingElements.forEach(ratingEl => {
        const imageId = ratingEl.dataset.imageId;
        
        ratingEl.addEventListener('click', function(e) {
            if (e.target.classList.contains('star')) {
                const rating = e.target.dataset.rating;
                
                // Send AJAX request to rate image
                fetch('api/rate_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        image_id: imageId,
                        rating: rating
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        updateRatingDisplay(ratingEl, rating);
                        showMessage('Rating saved!', 'success');
                    } else {
                        showMessage('Error saving rating', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Error saving rating', 'error');
                });
            }
        });
    });
    
    function updateRatingDisplay(ratingEl, rating) {
        const stars = ratingEl.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.innerHTML = '&#9733;'; // Filled star
                star.classList.add('rated');
            } else {
                star.innerHTML = '&#9734;'; // Empty star
                star.classList.remove('rated');
            }
        });
    }
}

// Follow buttons
function initFollowButtons() {
    const followButtons = document.querySelectorAll('.follow-btn');
    
    followButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const action = this.classList.contains('following') ? 'unfollow' : 'follow';
            
            fetch('api/follow_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button UI
                    if (action === 'follow') {
                        this.classList.add('following');
                        this.textContent = 'Following';
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-secondary');
                    } else {
                        this.classList.remove('following');
                        this.textContent = 'Follow';
                        this.classList.add('btn-outline-primary');
                        this.classList.remove('btn-secondary');
                    }
                    
                    // Update follow count if present
                    const countEl = document.querySelector(`[data-follow-count="${userId}"]`);
                    if (countEl) {
                        countEl.textContent = data.count;
                    }
                    
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message || 'Error processing request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error processing request', 'error');
            });
        });
    });
}

// Like buttons
function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.like-btn');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.targetId;
            const targetType = this.dataset.targetType; // 'session' or 'image'
            const action = this.classList.contains('liked') ? 'unlike' : 'like';
            
            fetch('api/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    target_id: targetId,
                    target_type: targetType,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button UI
                    if (action === 'like') {
                        this.classList.add('liked');
                        this.innerHTML = '<i class="bi bi-heart-fill text-danger"></i>';
                    } else {
                        this.classList.remove('liked');
                        this.innerHTML = '<i class="bi bi-heart"></i>';
                    }
                    
                    // Update like count
                    const countEl = this.closest('.card').querySelector('.likes-count');
                    if (countEl) {
                        countEl.textContent = data.count;
                    }
                    
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message || 'Error processing request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error processing request', 'error');
            });
        });
    });
}

// Drag and drop for image reordering
function initDragAndDrop() {
    const dragContainers = document.querySelectorAll('.drag-container');
    
    dragContainers.forEach(container => {
        let draggedItem = null;
        
        container.addEventListener('dragstart', function(e) {
            draggedItem = e.target;
            e.target.classList.add('dragging');
        });
        
        container.addEventListener('dragend', function(e) {
            e.target.classList.remove('dragging');
            draggedItem = null;
            
            // Save new order
            const items = Array.from(container.querySelectorAll('.draggable-item'));
            const newOrder = items.map(item => item.dataset.imageId);
            
            saveNewOrder(newOrder);
        });
        
        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            const currentDraggedItem = document.querySelector('.dragging');
            
            if (afterElement == null) {
                container.appendChild(currentDraggedItem);
            } else {
                container.insertBefore(currentDraggedItem, afterElement);
            }
        });
    });
    
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    function saveNewOrder(order) {
        fetch('api/reorder_images.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order: order
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showMessage('Error saving new order', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Notifications
function initNotifications() {
    // Mark notifications as read when clicked
    const notificationItems = document.querySelectorAll('.notification-item');
    
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            
            if (!this.dataset.read) {
                fetch('api/mark_notification_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        notification_id: notificationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.dataset.read = 'true';
                        this.classList.remove('unread');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
}

// Update online status every 30 seconds
function initOnlineStatus() {
    // Update status immediately on page load
    updateOnlineStatus();
    
    // Update every 30 seconds
    setInterval(updateOnlineStatus, 30000);
    
    function updateOnlineStatus() {
        fetch('api/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update'
            })
        })
        .catch(error => {
            console.error('Error updating status:', error);
        });
    }
}

// Search functionality
function initSearchFunctionality() {
    const searchForm = document.querySelector('#search-form');
    const searchInput = document.querySelector('#search-input');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            
            if (query) {
                // Simple search redirect - in a real app, you'd use AJAX
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
        });
    }
}

// Utility functions
function showMessage(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Initialize Bootstrap toast and show
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        document.body.removeChild(toast);
    });
}

// CSRF token for all AJAX requests
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           document.querySelector('input[name="csrf_token"]')?.value || 
           localStorage.getItem('csrf_token');
}

// Add CSRF token to all fetch requests
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const options = args[1] || {};
    const headers = options.headers || {};
    
    // Add CSRF token if not present
    if (!headers['X-CSRF-Token'] && getCSRFToken()) {
        headers['X-CSRF-Token'] = getCSRFToken();
        options.headers = headers;
    }
    
    args[1] = options;
    return originalFetch.apply(this, args);
};

// Form submission with CSRF protection
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.tagName === 'FORM') {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = getCSRFToken();
        form.appendChild(csrfInput);
    }
});