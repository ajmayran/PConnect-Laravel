/**
 * Distributor Notification Utilities
 * Handles notification operations for distributor accounts
 */

document.addEventListener('DOMContentLoaded', function () {
    // Load unread notification count when page loads
    fetchUnreadNotificationCount();

    // Set up real-time notifications if Pusher is available
    setupPusherNotifications();

    // Add click handler for mark all as read button
    const markAllReadBtn = document.getElementById('mark-all-notifications-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function (event) {
            event.preventDefault();
            markAllAsRead();
        });
    }
});

// Function to mark all notifications as read
function markAllAsRead() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update notification count
            fetchUnreadNotificationCount();
            
            // Refresh notification previews
            fetchNotificationsPreview();
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

// Function to fetch unread notification count
function fetchUnreadNotificationCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
        })
        .catch(error => console.error('Error fetching notification count:', error));
}

function updateNotificationBadge(count) {
    // Update notification badge on desktop
    const badge = document.getElementById('notification-badge');
    // Update notification badge on mobile
    const mobileBadge = document.getElementById('mobile-notification-badge');

    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    if (mobileBadge) {
        if (count > 0) {
            mobileBadge.textContent = count;
            mobileBadge.classList.remove('hidden');
        } else {
            mobileBadge.classList.add('hidden');
        }
    }
}

// Function to fetch notification preview
function fetchNotificationsPreview() {
    const container = document.getElementById('notifications-preview-container');
    if (!container) return;
    
    container.innerHTML = '<div class="p-3 text-sm text-center text-gray-500">Loading notifications...</div>';

    fetch('/notifications/latest')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Add success check based on your controller response
            const success = data && data.notifications !== undefined;
            
            if (!success || !data.notifications || data.notifications.length === 0) {
                container.innerHTML = '<div class="p-3 text-sm text-center text-gray-500">No notifications yet</div>';
                return;
            }

            let html = '';
            data.notifications.forEach(notification => {
                // Use is_read instead of read_at since you're using a custom model
                const isUnread = notification.is_read === false || notification.is_read === 0;
                const notificationDate = new Date(notification.created_at);
                const timeAgo = formatTimeAgo(notificationDate);
                const bgClass = isUnread ? 'bg-blue-50' : '';
                
                html += `
                <div class="p-3 ${bgClass} border-b border-gray-200">
                    <div class="flex items-start">
                        ${isUnread ? '<div class="flex-shrink-0 mr-2"><span class="block w-2 h-2 bg-blue-600 rounded-full"></span></div>' : ''}
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${notification.data.title || 'Notification'}</p>
                            <p class="text-xs text-gray-500 mt-1">${notification.data.message || ''}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">${timeAgo}</span>
                                ${isUnread ? `<button onclick="markAsRead(${notification.id}, event)" class="text-xs text-blue-600 hover:text-blue-800">Mark as read</button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            container.innerHTML = '<div class="p-3 text-sm text-center text-red-500">Failed to load notifications</div>';
        });
}

// Function to mark a notification as read
function markAsRead(notificationId, event) {
    // Stop event propagation if provided
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchUnreadNotificationCount();
                fetchNotificationsPreview();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
}

// Function to format time ago
function formatTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " years ago";
    
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " months ago";
    
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " days ago";
    
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hours ago";
    
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " minutes ago";
    
    return Math.floor(seconds) + " seconds ago";
}

// Setup Pusher for real-time notifications
function setupPusherNotifications() {
    // Check if Pusher and user ID are available
    if (window.pusherAppKey && window.pusherAppCluster && window.userId) {
        try {
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

            // Subscribe to the private channel for this user
            const channel = pusher.subscribe(`private-notifications.${window.userId}`);

            // Listen for new notification events
            channel.bind('notification.new', function (data) {
                // Show a notification toast
                showNotificationToast(data);

                // Update the notification count
                fetchUnreadNotificationCount();

                // Update the notification preview if visible
                if (!document.getElementById('notificationsPopup').classList.contains('hidden')) {
                    fetchNotificationsPreview();
                }
            });
        } catch (error) {
            console.error('Error setting up Pusher notifications:', error);
        }
    }
}

// Function to show a notification toast
function showNotificationToast(data) {
    // Create a toast container if it doesn't exist
    let toastContainer = document.getElementById('notification-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'notification-toast-container';
        toastContainer.className = 'fixed z-50 flex flex-col space-y-2 right-4 bottom-4';
        document.body.appendChild(toastContainer);
    }

    // Create a new toast
    const toast = document.createElement('div');
    toast.className = 'max-w-sm p-4 mb-2 bg-white border border-green-100 rounded-lg shadow-lg animate__animated animate__fadeInUp';
    toast.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${data.data.title || 'New Notification'}</p>
                <p class="mt-1 text-sm text-gray-500">${data.data.message || ''}</p>
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

    // Add close functionality
    toast.querySelector('.close-toast').addEventListener('click', function() {
        removeToast(toast);
    });

    // Add to container
    toastContainer.appendChild(toast);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

// Function to remove a toast with animation
function removeToast(toast) {
    toast.classList.replace('animate__fadeInUp', 'animate__fadeOutDown');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 500);
}