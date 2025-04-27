/**
 * Retailer Notification Utilities
 * Handles notification operations for retailer accounts
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
    
    fetch('/retailers/notifications/mark-all-read', {
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
    fetch('/retailers/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
        })
        .catch(error => console.error('Error fetching notification count:', error));
}

function updateNotificationBadge(count) {
    // Desktop badge
    const badge = document.getElementById('notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Mobile badge
    const mobileBadge = document.getElementById('mobile-notification-badge');
    if (mobileBadge) {
        if (count > 0) {
            mobileBadge.textContent = count > 99 ? '99+' : count;
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

    fetch('/retailers/notifications/latest')
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
                const bgClass = isUnread ? 'bg-blue-50' : '';

                html += `
                <div class="p-3 ${bgClass} border-b border-gray-200">
                    <div class="flex items-start">
                        ${isUnread ? '<div class="flex-shrink-0 mr-2"><span class="block w-2 h-2 bg-blue-600 rounded-full"></span></div>' : ''}
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${notification.title || 'Notification'}</p>
                            <p class="text-xs text-gray-500 mt-1">${notification.message || ''}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">${formatTimeAgo(new Date(notification.created_at))}</span>
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

// Function to mark a notification as read (removed duplicate)
function markAsRead(notificationId, event) {
    // Stop event propagation if provided
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/retailers/notifications/mark-read', {
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
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
    const diffMinutes = Math.floor(diffTime / (1000 * 60));

    if (diffDays > 0) {
        return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    } else if (diffHours > 0) {
        return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    } else if (diffMinutes > 0) {
        return `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''} ago`;
    } else {
        return 'Just now';
    }
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
    // Create toast element
    const toast = document.createElement('div');
    toast.classList.add('fixed', 'right-4', 'top-20', 'bg-white', 'shadow-lg', 'rounded-lg', 'p-4', 'z-50', 'border-l-4', 'border-green-500', 'notification-toast');
    toast.style.maxWidth = '300px';
    toast.style.transform = 'translateX(400px)';
    toast.style.opacity = '0';
    toast.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';

    toast.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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