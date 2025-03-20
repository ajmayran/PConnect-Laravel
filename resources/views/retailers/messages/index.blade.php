<x-app-layout>
    <x-dashboard-nav />
    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-retailer-sidebar />
            <!-- Main Content Area -->

            <div class="p-3 bg-white border-b border-gray-200 rounded-lg shadow-sm md:p-5">

                <h2 class="mb-4 text-xl font-semibold text-gray-800">Messages</h2>

                <div class="flex flex-col md:flex-row h-[calc(100vh-240px)]">
                    <!-- Conversations List -->
                    <div id="conversations-list"
                        class="w-full mb-4 overflow-y-auto border-gray-200 md:pr-3 md:border-r md:w-80 md:max-w-xs md:mb-0">
                        <div class="flex items-center justify-between mb-3 md:hidden">
                            <h3 class="font-medium text-gray-700 text-md">Conversations</h3>
                            <button id="toggle-conversations" class="text-gray-500 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        @php
                            // Filter distributors to only show those with messages
                            $distributorsWithMessages = $distributors->filter(function ($distributor) {
                                return $distributor->latestMessage !== null;
                            });
                        @endphp

                        @forelse ($distributorsWithMessages as $distributor)
                            <a href="{{ route('retailers.messages.index', ['distributor' => $distributor->id]) }}"
                                class="block p-3 mb-2 transition-all duration-200 hover:bg-gray-50 rounded-lg {{ $currentDistributor && $currentDistributor->id == $distributor->id ? 'bg-gradient-to-r from-green-50 to-gray-100 border-l-4 border-green-500' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="relative flex-shrink-0">
                                        @if ($distributor->distributor->company_profile_image)
                                            <img src="{{ Storage::url($distributor->distributor->company_profile_image) }}"
                                                alt="{{ $distributor->distributor->company_name }}"
                                                class="object-cover w-12 h-12 rounded-full border-2 {{ $currentDistributor && $currentDistributor->id == $distributor->id ? 'border-green-500' : 'border-gray-200' }}">
                                        @else
                                            <div
                                                class="flex items-center justify-center w-12 h-12 text-gray-600 bg-gray-200 rounded-full border-2 {{ $currentDistributor && $currentDistributor->id == $distributor->id ? 'border-green-500' : 'border-gray-200' }}">
                                                {{ strtoupper(substr($distributor->distributor->company_name, 0, 2)) }}
                                            </div>
                                        @endif

                                        @if ($distributor->unreadCount > 0)
                                            <span
                                                class="absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-500 rounded-full shadow-sm -top-1 -right-1 animate-pulse">
                                                {{ $distributor->unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 {{ $distributor->unreadCount > 0 ? 'font-bold' : '' }}">
                                            {{ $distributor->distributor->company_name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-600 truncate {{ $distributor->unreadCount > 0 ? 'font-semibold' : '' }}">
                                            @if ($distributor->latestMessage->sender_id == Auth::id())
                                                <span class="text-gray-400">You: </span>
                                            @endif
                                            {{ \Illuminate\Support\Str::limit($distributor->latestMessage->message, 35) }}
                                        </p>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $distributor->latestMessage->created_at->diffForHumans(null, true, true) }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center p-8 text-center rounded-lg bg-gray-50">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <p class="text-gray-500">No conversations yet</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Chat Area -->
                    <div class="flex flex-col flex-1" id="chat-area">
                        @if ($currentDistributor)
                            <!-- Chat Header -->
                            <div
                                class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b border-gray-200">
                                <div class="flex items-center">
                                    <button id="back-to-conversations"
                                        class="mr-2 text-gray-500 md:hidden focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="flex-shrink-0">
                                        @if ($currentDistributor->distributor->company_profile_image)
                                            <img src="{{ Storage::url($currentDistributor->distributor->company_profile_image) }}"
                                                alt="{{ $currentDistributor->distributor->company_name }}"
                                                class="object-cover w-10 h-10 border-2 border-green-500 rounded-full">
                                        @else
                                            <div
                                                class="flex items-center justify-center w-10 h-10 text-gray-600 bg-gray-200 border-2 border-green-500 rounded-full">
                                                {{ strtoupper(substr($currentDistributor->distributor->company_name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">
                                            {{ $currentDistributor->distributor->company_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="inline-block w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                            Online
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <button class="text-gray-500 focus:outline-none hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div id="messages-container" class="flex-1 p-4 space-y-4 overflow-y-auto bg-gray-50">
                                @forelse ($messages as $message)
                                    <div
                                        class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }} group">
                                        <div
                                            class="{{ $message->sender_id == Auth::id()
                                                ? 'message-bubble-sent bg-gradient-to-br from-green-50 to-green-100 text-green-900'
                                                : 'message-bubble-received bg-white text-gray-800' }} 
                                            rounded-lg p-3 shadow-sm max-w-xs lg:max-w-md transform transition-transform duration-200 hover:scale-[1.02]">
                                            <p class="text-sm">{{ $message->message }}</p>
                                            <p
                                                class="mt-1 text-xs {{ $message->sender_id == Auth::id() ? 'text-green-700/70' : 'text-gray-500' }} flex items-center">
                                                {{ $message->created_at->format('h:i A') }}
                                                @if ($message->sender_id == Auth::id())
                                                    <span
                                                        class="ml-1 transition-opacity opacity-0 group-hover:opacity-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex items-center justify-center h-full">
                                        <div class="p-6 text-center bg-white rounded-lg shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <p class="text-gray-500">Start a conversation with
                                                {{ $currentDistributor->distributor->company_name }}</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Message Input -->
                            <div class="p-3 bg-white border-t border-gray-200">
                                <form id="message-form" class="flex space-x-2">
                                    <input type="hidden" name="receiver_id" value="{{ $currentDistributor->id }}">
                                    <div class="relative flex-1">
                                        <input type="text" name="message" id="message-input"
                                            class="block w-full pl-4 pr-10 border-gray-300 rounded-full shadow-sm focus:border-green-500 focus:ring-green-500"
                                            placeholder="Type your message..." required>
                                        <div
                                            class="absolute flex space-x-1 transform -translate-y-1/2 right-2 top-1/2">
                                            <button type="button" class="text-gray-400 hover:text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.414 0 3 3 0 014.242 0 1 1 0 001.414-1.414 5 5 0 00-7.07 0 1 1 0 000 1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 text-white transition-colors duration-200 bg-green-600 border border-transparent rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="flex items-center justify-center h-full bg-gray-50">
                                <div class="p-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <h3 class="mb-1 text-lg font-medium text-gray-900">No conversation selected</h3>
                                    <p class="text-gray-500">Select a distributor from the list to start messaging</p>
                                    <button
                                        class="px-4 py-2 mt-4 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                                        Find Distributors
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get DOM elements
                const messagesContainer = document.getElementById('messages-container');
                const conversationsList = document.getElementById('conversations-list');
                const chatArea = document.getElementById('chat-area');
                const toggleButton = document.getElementById('toggle-conversations');
                const backButton = document.getElementById('back-to-conversations');

                // Handle mobile navigation
                if (toggleButton) {
                    toggleButton.addEventListener('click', function() {
                        conversationsList.classList.toggle('conversation-list-collapsed');
                    });
                }

                if (backButton) {
                    backButton.addEventListener('click', function() {
                        // Show conversations, hide chat area on mobile
                        if (window.innerWidth < 768) {
                            conversationsList.classList.remove('hidden');
                            chatArea.classList.add('hidden');
                        }
                    });
                }

                // Handle conversation item clicks on mobile
                const conversationItems = document.querySelectorAll('#conversations-list a');
                conversationItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            // Hide conversation list, show chat area
                            conversationsList.classList.add('hidden');
                            chatArea.classList.remove('hidden');
                        }
                    });
                });

                // Scroll to bottom of messages
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }

                // Handle form submission
                const messageForm = document.getElementById('message-form');
                if (messageForm) {
                    messageForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const receiverId = this.querySelector('input[name="receiver_id"]').value;
                        const messageInput = document.getElementById('message-input');
                        const message = messageInput.value;

                        if (!message.trim()) return;

                        // Clear the input right away for better UX
                        messageInput.value = '';

                        // Create and add message to UI immediately with animation
                        const tempMessageDiv = document.createElement('div');
                        tempMessageDiv.className = 'flex justify-end new-message-animation';
                        tempMessageDiv.innerHTML = `
                        <div class="message-bubble-sent bg-gradient-to-br from-green-50 to-green-100 text-green-900 rounded-lg p-3 shadow-sm max-w-xs lg:max-w-md transform transition-transform duration-200 hover:scale-[1.02]">
                            <p class="text-sm">${message}</p>
                            <p class="flex items-center mt-1 text-xs text-green-700/70">
                                ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                <span class="ml-1 transition-opacity opacity-0 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </p>
                        </div>
                    `;

                        messagesContainer.appendChild(tempMessageDiv);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;

                        // Send the message to the server
                        fetch("{{ route('retailers.messages.send') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    receiver_id: receiverId,
                                    message: message
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status !== 'success') {
                                    console.error('Failed to send message:', data);
                                    tempMessageDiv.remove();
                                    alert('Failed to send message. Please try again.');
                                } else {
                                    // Update latest message preview in sidebar
                                    updateMessagePreview(receiverId, message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                tempMessageDiv.remove();
                                alert('Failed to send message. Please check your connection.');
                            });
                    });
                }

                // Function to update message preview after sending
                function updateMessagePreview(receiverId, message) {
                    const distributorItem = document.querySelector(`a[href*="distributor=${receiverId}"]`);
                    if (distributorItem) {
                        // Update message preview text
                        const previewElement = distributorItem.querySelector('.text-xs.text-gray-600');
                        if (previewElement) {
                            previewElement.innerHTML =
                                `<span class="text-gray-400">You: </span>${message.length > 35 ? message.substring(0, 32) + '...' : message}`;
                        }

                        // Update timestamp
                        const timestampElement = distributorItem.querySelector('.text-xs.text-gray-400');
                        if (timestampElement) {
                            timestampElement.textContent = 'just now';
                        }

                        // Move the conversation to the top of the list
                        const parentList = distributorItem.parentNode;
                        if (parentList && parentList.firstChild !== distributorItem) {
                            parentList.insertBefore(distributorItem, parentList.firstChild);
                        }
                    }
                }

                // Setup Echo listener for real-time messages
                if (window.Echo) {
                    window.Echo.private(`chat.{{ Auth::id() }}`)
                        .listen('MessageSent', function(data) {
                            console.log('Received message event via Echo:', data);
                            
                            const currentDistributorId = {{ $currentDistributor->id ?? 0 }};

                            // Debug info
                            console.log('Current distributor:', currentDistributorId);
                            console.log('Message sender:', data.senderId);
                            console.log('Message content:', data.message);

                            // Check if the message is from the current distributor
                            if (data.senderId == currentDistributorId) {
                                // Add the message to the current chat
                                const messageDiv = document.createElement('div');
                                messageDiv.className = 'flex justify-start new-message-animation';
                                messageDiv.innerHTML = `
                                <div class="message-bubble-received bg-white text-gray-800 rounded-lg p-3 shadow-sm max-w-xs lg:max-w-md transform transition-transform duration-200 hover:scale-[1.02]">
                                    <p class="text-sm">${data.message}</p>
                                    <p class="mt-1 text-xs text-gray-500">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                                </div>
                            `;

                                if (messagesContainer) {
                                    messagesContainer.appendChild(messageDiv);
                                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                                }

                                // Mark as read
                                fetch("{{ route('retailers.messages.mark-read') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        sender_id: data.senderId
                                    })
                                }).catch(error => {
                                    console.error('Error marking message as read:', error);
                                });

                                // Update message preview
                                updateIncomingMessagePreview(data.senderId, data.message, false);
                            } else {
                                // Update message preview with unread indicator
                                updateIncomingMessagePreview(data.senderId, data.message, true);
                            }

                            // Update unread count in the navbar
                            updateUnreadCount();
                        });
                } else {
                    console.error('❌ Laravel Echo is NOT initialized - this may be a timing issue. Check your app.js');
                }

                // Function to update message preview when receiving a message
                function updateIncomingMessagePreview(senderId, message, isUnread) {
                    const distributorItem = document.querySelector(`a[href*="distributor=${senderId}"]`);
                    if (distributorItem) {
                        // Update message preview text
                        const previewElement = distributorItem.querySelector('.text-xs.text-gray-600');
                        if (previewElement) {
                            previewElement.textContent = message.length > 35 ? message.substring(0, 32) +
                                '...' :
                                message;

                            if (isUnread) {
                                previewElement.classList.add('font-semibold');
                            }
                        }

                        // Update timestamp
                        const timestampElement = distributorItem.querySelector('.text-xs.text-gray-400');
                        if (timestampElement) {
                            timestampElement.textContent = 'just now';
                        }

                        // Update unread indicator
                        if (isUnread) {
                            const avatarContainer = distributorItem.querySelector('.relative');
                            let unreadBadge = avatarContainer.querySelector('.absolute');

                            if (!unreadBadge) {
                                // Create new badge
                                unreadBadge = document.createElement('span');
                                unreadBadge.className =
                                    'absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-500 rounded-full animate-pulse shadow-sm';
                                unreadBadge.textContent = '1';
                                avatarContainer.appendChild(unreadBadge);
                            } else {
                                // Increment badge count
                                const count = parseInt(unreadBadge.textContent) || 0;
                                unreadBadge.textContent = count + 1;
                            }

                            // Make distributor name bold
                            const nameElement = distributorItem.querySelector('.text-sm.font-medium');
                            if (nameElement) {
                                nameElement.classList.add('font-bold');
                            }
                        }

                        // Move the conversation to the top of the list
                        const parentList = distributorItem.parentNode;
                        if (parentList && parentList.firstChild !== distributorItem) {
                            parentList.insertBefore(distributorItem, parentList.firstChild);
                        }
                    } else {
                        // This is a new conversation, reload to show it
                        window.location.reload();
                    }
                }

                // Function to update unread message count
                function updateUnreadCount() {
                    fetch("{{ route('retailers.messages.unread-count') }}")
                        .then(response => response.json())
                        .then(data => {
                            const unreadBadge = document.getElementById('unread-message-badge');
                            if (unreadBadge) {
                                if (data.unread_count > 0) {
                                    unreadBadge.innerText = data.unread_count;
                                    unreadBadge.classList.remove('hidden');
                                } else {
                                    unreadBadge.classList.add('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error updating unread count:', error);
                        });
                }

                // Responsive behavior for window resizing
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        // If on desktop, make sure both panels are visible
                        if (conversationsList) conversationsList.classList.remove('hidden');
                        if (chatArea) chatArea.classList.remove('hidden');
                    }
                });

                // Initial unread count
                updateUnreadCount();
            });

            // Add Echo status check outside DOMContentLoaded to run immediately
            setTimeout(function checkEcho() {
                if (window.Echo) {
                    console.log('✅ Laravel Echo is initialized');

                    // Check connection state if possible
                    if (window.Echo.connector && window.Echo.connector.pusher) {
                        console.log('Echo connection state:', window.Echo.connector.pusher.connection.state);
                    }
                } else {
                    console.error('❌ Laravel Echo is NOT initialized');
                    // Try again in another second (in case of delayed initialization)
                    setTimeout(checkEcho, 1000);
                }
            }, 500);
        </script>
    @endpush

    <style>
        /* Make scrollbar more subtle */
        #messages-container::-webkit-scrollbar {
            width: 5px;
        }

        #messages-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #messages-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 5px;
        }

        #messages-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Message bubble styles */
        .message-bubble-sent {
            position: relative;
            border-radius: 18px 18px 4px 18px;
        }

        .message-bubble-received {
            position: relative;
            border-radius: 18px 18px 18px 4px;
        }

        /* Fix message input position on mobile */
        @media (max-width: 768px) {

            /* Container height adjustments */
            .flex.flex-col.md\:flex-row.h-\[calc\(100vh-240px\)\] {
                height: calc(100vh - 180px);
                display: flex;
                flex-direction: column;
            }

            #chat-area {
                display: flex;
                flex-direction: column;
                height: 100%;
                position: relative;
            }

            #messages-container {
                flex: 1;
                overflow-y: auto;
                max-height: calc(100vh - 300px);
                padding-bottom: 10px;
            }

            /* Keep the input naturally positioned but styled properly */
            .p-3.bg-white.border-t.border-gray-200 {
                background-color: white;
                z-index: 10;
                box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
                width: calc(100% + 2rem);
                /* Expand beyond parent container */
                margin-left: -1rem;
                margin-right: -1rem;
                padding-left: 1rem;
                padding-right: 1rem;
                position: relative;
                /* Add this */
                left: 0;
                /* Position properly */
            }

            /* Make sure the parent container doesn't clip the input area */
            .p-3.bg-white.border-b.border-gray-200.rounded-lg.shadow-sm.md\:p-5 {
                overflow: visible !important;
                /* Prevent clipping */
                position: relative;
            }

            /* Fix input form to take full available width */
            #message-form {
                width: 100%;
                display: flex;
            }

            /* Fix rounded corners of input for better mobile display */
            #message-input {
                width: 100%;
                border-radius: 20px;
            }

            /* Make sure the page has proper spacing at the bottom for footer */
            x-footer {
                margin-top: 20px;
            }
        }

        /* Animation for new messages */
        .new-message-animation {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-app-layout>
<x-footer />
