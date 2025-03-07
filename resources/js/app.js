import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
Alpine.start();

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    callback(false, response.data);
                })
                .catch(error => {
                    callback(true, error);
                });
            }
        };
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Check for message badge element
    const unreadBadge = document.getElementById('unread-message-badge');
    
    if (unreadBadge && window.Echo) {
        // Set up Echo to listen for messages if user is authenticated
        const userId = document.body.getAttribute('data-user-id');
        
        if (userId) {
            window.Echo.private(`chat.${userId}`)
                .listen('.message.sent', (e) => {
                    // Update badge count
                    let currentCount = parseInt(unreadBadge.textContent || '0');
                    currentCount += 1;
                    unreadBadge.textContent = currentCount;
                    unreadBadge.classList.remove('hidden');
                    
                    // Show notification if supported
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('New Message', { 
                            body: 'You have received a new message'
                        });
                    }
                });
                
            // Update unread count periodically
            setInterval(() => {
                const userType = document.body.getAttribute('data-user-type');
                const countUrl = userType === 'retailer' 
                    ? '/retailers/messages/unread-count' 
                    : '/messages/unread-count';
                    
                fetch(countUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.unread_count > 0) {
                            unreadBadge.textContent = data.unread_count;
                            unreadBadge.classList.remove('hidden');
                        } else {
                            unreadBadge.classList.add('hidden');
                        }
                    });
            }, 30000); // Check every 30 seconds
        }
    }
});