// StageOne Modeling Portal - Messages JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages-container');
    const recipientIdInput = document.getElementById('recipient-id');
    
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            const recipientId = recipientIdInput.value;
            
            if (message && recipientId) {
                sendMessage(message, recipientId);
                messageInput.value = '';
            }
        });
    }
    
    // Auto-scroll to bottom of messages
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Check for new messages every 10 seconds
    if (recipientIdInput) {
        setInterval(checkNewMessages, 10000);
    }
});

function sendMessage(message, recipientId) {
    fetch('/api/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            recipientId: parseInt(recipientId),
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add message to the chat interface
            addMessageToChat(data.message, true);
        } else {
            alert(data.error || 'Error sending message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the message');
    });
}

function addMessageToChat(messageData, isOwnMessage) {
    const messagesContainer = document.getElementById('messages-container');
    
    if (!messagesContainer) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex mb-3 ${isOwnMessage ? 'justify-content-end' : 'justify-content-start'}`;
    
    let content = '';
    if (!isOwnMessage) {
        content += `
            <img src="${messageData.sender_avatar}" class="avatar-sm me-2" alt="${messageData.sender_username}">
        `;
    }
    
    content += `
        <div class="message-bubble p-3 rounded ${isOwnMessage ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 70%;">
            <div>${messageData.content}</div>
            <small class="d-block mt-1 opacity-75">
                ${formatTime(messageData.created_at)}
            </small>
        </div>
    `;
    
    if (isOwnMessage) {
        content += `
            <img src="${messageData.sender_avatar}" class="avatar-sm ms-2" alt="${messageData.sender_username}">
        `;
    }
    
    messageDiv.innerHTML = content;
    messagesContainer.appendChild(messageDiv);
    
    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function checkNewMessages() {
    const recipientId = document.getElementById('recipient-id');
    if (!recipientId) return;
    
    const recipientIdValue = recipientId.value;
    
    fetch(`/api/get_messages.php?recipientId=${recipientIdValue}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the messages display
            updateMessagesDisplay(data.messages);
        }
    })
    .catch(error => {
        console.error('Error checking for new messages:', error);
    });
}

function updateMessagesDisplay(messages) {
    const messagesContainer = document.getElementById('messages-container');
    if (!messagesContainer) return;
    
    // Clear current messages
    messagesContainer.innerHTML = '';
    
    // Add each message
    messages.forEach(message => {
        const isOwnMessage = message.sender_id == currentUserId;
        addMessageToChat(message, isOwnMessage);
    });
}