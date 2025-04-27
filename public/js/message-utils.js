/**
 * PConnect Message Utilities
 * Global functions for handling messaging functionality across the application
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on a page with message functionality
    if (typeof updateUnreadCount === 'function') {
        // We're already on the messages page, which has its own implementation
        console.log('Message page detected, using built-in updateUnreadCount');
    } else {
        // We're on another page, so we'll use our global implementation
        console.log('Adding global message update functionality');

        // Set up polling for unread messages
        fetchAndUpdateUnreadCount();
        setInterval(fetchAndUpdateUnreadCount, 1000);

        // Set up Pusher if available
        setupPusherForMessages();
    }
});

/**     
 * Fetches the latest unread message count and updates badges
 */
function fetchAndUpdateUnreadCount() {
    // Show indicator if it exists
    const indicator = document.getElementById('message-loading-indicator');
    if (indicator) indicator.classList.remove('hidden');

    fetch('/retailers/messages/unread-count')
        .then(response => {
            if (!response.ok) {
                console.warn('Unread count API returned status:', response.status);
                return { success: false, count: 0, unread_count: 0 };
            }
            return response.json();
        })
        .then(data => {
            // Log the full response for debugging
            // console.log('Unread count API response:', data);

            if (data.success) {
                // Use unread_count which should be the senders count
                updateAllMessageBadges(data.unread_count || 0);
            } else {
                console.warn('API returned error:', data.message);
                updateAllMessageBadges(0);
            }
        })
        .catch(error => {
            console.error('Error fetching unread message count:', error);
        })
        .finally(() => {
            // Hide indicator
            if (indicator) {
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 300);
            }
        });
}

/**
 * Updates all message badges in the application with the given count
 * @param {number} count - Number of unread messages
 */
function updateAllMessageBadges(count) {
    try {
        const badges = document.querySelectorAll('[id$="unread-message-badge"]');

        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
    } catch (error) {
        console.error('Error updating message badges:', error);
    }
}

/**
 * Sets up Pusher to listen for new messages
 */
function setupPusherForMessages() {
    // Only proceed if Echo is initialized and we have a user ID
    if (window.Echo && window.userId) {
        console.log('Setting up Echo listener for messages');

        // Check if we're on the messages page (has its own implementation)
        const messagesContainer = document.getElementById('messages-container');
        const isMessagesPage = document.querySelector('.retailers-messages-page') !== null;

        if (isMessagesPage) {
            console.log('On messages page - skipping global Echo listener setup');
            return; // Skip setup on messages page
        }

        // Use Echo to listen on the private channel
        window.Echo.private(`chat.${window.userId}`)
            .listen('.message.sent', function (data) {
                console.log('New message received via Echo:', data);

                // Update unread count
                fetchAndUpdateUnreadCount();

                // Show toast notification
                showMessageToast(data);

                // Append message directly if on a page with the message container
                if (messagesContainer) {
                    appendMessageToChat(data);
                }
            });

        console.log('✅ Echo listener setup complete');
    } else {
        console.error('❌ Echo not initialized or userId not available');
    }
}

/**
 * Updates chat messages on the page if the chat container is visible
 */
function appendMessageToChat(data) {
    const messagesContainer = document.getElementById('messages-container');
    if (!messagesContainer) {
        console.warn('⚠️ Chat container not found!');
        return;
    }

    console.log('🔄 Appending message:', data);

    // Create message element
    const messageDiv = document.createElement('div');

    // Set appropriate classes based on who sent the message
    if (data.senderId == window.userId) {
        // Outgoing message
        messageDiv.className = 'flex justify-end';
        messageDiv.innerHTML = `
        <div class="max-w-xs p-3 bg-green-100 rounded-lg shadow lg:max-w-md">
            <p class="text-sm">${data.message}</p>
            <p class="mt-1 text-xs text-gray-500">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
        </div>
        `;
    } else {
        // Incoming message
        messageDiv.className = 'flex justify-start';
        messageDiv.innerHTML = `
        <div class="max-w-xs p-3 bg-white rounded-lg shadow lg:max-w-md">
            <p class="text-sm">${data.message}</p>
            <p class="mt-1 text-xs text-gray-500">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
        </div>
        `;

        // Mark message as read
        markMessageAsRead(data.senderId);
    }

    // Append to container
    messagesContainer.appendChild(messageDiv);

    // Scroll to the latest message
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

/**
 * Marks messages from a sender as read
 */
function markMessageAsRead(senderId) {
    const userType = document.body.getAttribute('data-user-type');
    const endpoint = userType === 'retailer'
        ? '/retailers/messages/mark-read'
        : '/distributors/messages/mark-read';

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ sender_id: senderId })
    }).catch(error => {
        console.error('Error marking message as read:', error);
    });
}

function showMessageToast(data) {
    // Create toast element
    const toast = document.createElement('div');
    toast.classList.add('fixed', 'right-4', 'top-20', 'bg-white', 'shadow-lg', 'rounded-lg', 'p-4', 'z-50', 'border-l-4', 'border-blue-500');
    toast.style.maxWidth = '300px';
    toast.style.transform = 'translateX(400px)';
    toast.style.opacity = '0';
    toast.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';

    // Construct toast content
    const senderName = data.sender_name || 'Someone';
    const message = data.message || 'sent you a message';

    toast.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${senderName}</p>
                <p class="mt-1 text-sm text-gray-500">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button class="close-toast inline-flex rounded-md p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;

    // Add to document
    document.body.appendChild(toast);

    // Add slide-in animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 100);

    // Close button functionality
    toast.querySelector('.close-toast').addEventListener('click', () => {
        removeToast(toast);
    });

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 5000);

    // Add click handler to go to messages
    toast.addEventListener('click', function (e) {
        if (!e.target.closest('.close-toast')) {
            window.location.href = `/retailers/messages?distributor=${data.sender_id}`;
        }
    });
}

function removeToast(toast) {
    toast.style.transform = 'translateX(400px)';
    toast.style.opacity = '0';
    setTimeout(() => {
        if (document.body.contains(toast)) {
            document.body.removeChild(toast);
        }
    }, 300);
}
