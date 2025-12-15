// StageOne Modeling Portal - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Update user's last active status every 30 seconds
    if (typeof userId !== 'undefined') {
        setInterval(updateLastActive, 30000);
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Update last active status
function updateLastActive() {
    fetch('/api/update_last_active.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId
        })
    })
    .catch(error => {
        console.error('Error updating last active:', error);
    });
}

// Like a session
function likeSession(sessionId) {
    fetch('/api/like_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sessionId: sessionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the like button count
            const likeButton = document.querySelector(`[onclick="likeSession(${sessionId})"]`);
            if (likeButton) {
                const heartIcon = likeButton.querySelector('i');
                heartIcon.classList.toggle('fas');
                heartIcon.classList.toggle('far');
                
                // Update count
                const countSpan = likeButton.querySelector('span');
                if (countSpan) {
                    countSpan.textContent = data.newCount;
                } else {
                    likeButton.innerHTML = `<i class="fas fa-heart"></i> ${data.newCount}`;
                }
            }
        } else {
            alert(data.error || 'Error liking session');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while liking the session');
    });
}

// Follow a user
function follow(userId) {
    fetch('/api/follow_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the follow button
            const followButton = document.querySelector(`[onclick="follow(${userId})"]`);
            if (followButton) {
                followButton.textContent = 'Unfollow';
                followButton.className = 'btn btn-sm btn-outline-secondary';
                followButton.setAttribute('onclick', `unfollow(${userId})`);
            }
            
            // Create notification
            createNotification(userId, 'follow', `${currentUser.username} started following you`);
        } else {
            alert(data.error || 'Error following user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while following the user');
    });
}

// Unfollow a user
function unfollow(userId) {
    fetch('/api/unfollow_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the unfollow button
            const unfollowButton = document.querySelector(`[onclick="unfollow(${userId})"]`);
            if (unfollowButton) {
                unfollowButton.textContent = 'Follow';
                unfollowButton.className = 'btn btn-sm btn-primary';
                unfollowButton.setAttribute('onclick', `follow(${userId})`);
            }
        } else {
            alert(data.error || 'Error unfollowing user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while unfollowing the user');
    });
}

// Send a message
function sendMessage(userId) {
    const message = prompt('Enter your message:');
    if (message) {
        fetch('/api/send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                recipientId: userId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Message sent successfully');
            } else {
                alert(data.error || 'Error sending message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the message');
        });
    }
}

// Create notification
function createNotification(userId, type, message) {
    fetch('/api/create_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId,
            type: type,
            message: message
        })
    })
    .catch(error => {
        console.error('Error creating notification:', error);
    });
}

// Rate an item (session or image)
function rateItem(targetType, targetId, rating) {
    fetch('/api/rate_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            targetType: targetType,
            targetId: targetId,
            rating: rating
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rating submitted successfully');
        } else {
            alert(data.error || 'Error rating item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rating the item');
    });
}