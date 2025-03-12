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
        setInterval(fetchAndUpdateUnreadCount, 30000);

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
            if (data.success) {
                updateAllMessageBadges(data.unread_count || data.count || 0);
            } else {
                console.warn('API returned error:', data.message);
                // Still update badges but with 0 to avoid showing stale data
                updateAllMessageBadges(0);
            }
        })
        .catch(error => {
            console.error('Error fetching unread message count:', error);
            // Don't update badges or set to 0 on error - keep existing state
        })
        .finally(() => {
            // Hide indicator
            if (indicator) {
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 300); // Short delay for visual feedback
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
    // Only proceed if Pusher is loaded and we have a user ID
    if (window.Pusher && window.userId) {
        const pusher = new Pusher(window.pusherAppKey, {
            cluster: window.pusherAppCluster,
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });

        // Subscribe to the private channel
        try {
            const channel = pusher.subscribe(`private-chat.${window.userId}`);

            // Listen for new messages
            channel.bind('message.sent', function (data) {
                console.log('New message received:', data);

                // Update unread count when receiving a new message
                fetchAndUpdateUnreadCount();
            });
        } catch (error) {
            console.error('Error setting up Pusher:', error);
        }
    }
}