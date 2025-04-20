<x-distributor-layout>
    @php
        // Define $isBlocked at the template level so it's available everywhere
$isBlocked = isset($currentRetailer)
    ? App\Models\BlockedMessage::where('distributor_id', Auth::id())
        ->where('retailer_id', $currentRetailer->id)
                ->exists()
            : false;
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex h-[calc(100vh-200px)]">
                        <!-- Retailers Sidebar -->
                        <div class="w-1/4 overflow-y-auto bg-white border-r border-gray-200">
                            <div class="p-4">
                                <h2 class="mb-4 font-semibold text-gray-700">Retailers</h2>
                                <div class="space-y-2">
                                    @forelse ($retailers as $retailer)
                                        <a href="{{ route('distributors.messages.index', ['retailer' => $retailer->id]) }}"
                                            class="block rounded-md p-2 hover:bg-gray-100 {{ $currentRetailer && $currentRetailer->id == $retailer->id ? 'bg-green-100' : '' }}">
                                            <div class="flex items-center space-x-3">
                                                <div class="relative flex-shrink-0">
                                                    @if ($retailer->retailerProfile->profile_picture)
                                                        <img src="{{ Storage::url($retailer->retailerProfile->profile_picture) }}"
                                                            alt="{{ $retailer->retailerProfile->company_name }}"
                                                            class="object-cover w-10 h-10 rounded-full">
                                                    @else
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 text-gray-600 bg-gray-200 rounded-full">
                                                            {{ strtoupper(substr($retailer->first_name, 0, 2)) }}
                                                        </div>
                                                    @endif

                                                    @if ($retailer->unread_count > 0)
                                                        <span
                                                            class="absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                                                            {{ $retailer->unread_count }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $retailer->first_name }} {{ $retailer->last_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 truncate">
                                                        {{ $retailer->retailerProfile->business_name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="flex flex-col items-center justify-center h-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-4 text-gray-300"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            <p class="mb-1 text-gray-500">Select a retailer to view your conversation
                                            </p>
                                            <p class="text-sm text-gray-400">Only retailers you've messaged with are
                                                shown</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="flex flex-col w-3/4 bg-gray-50">
                            @if ($currentRetailer)
                                <!-- Chat Header -->
                                <div class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if ($currentRetailer->retailerProfile->profile_picture)
                                                <img src="{{ Storage::url($currentRetailer->retailerProfile->profile_picture) }}"
                                                    alt="{{ $currentRetailer->first_name }}"
                                                    class="object-cover w-10 h-10 rounded-full">
                                            @else
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 text-gray-600 bg-gray-200 rounded-full">
                                                    {{ strtoupper(substr($currentRetailer->first_name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $currentRetailer->first_name }} {{ $currentRetailer->last_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $currentRetailer->retailerProfile->business_name }}
                                                @if ($currentRetailer->retailerProfile->phone)
                                                    • {{ $currentRetailer->retailerProfile->phone }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- More Options Menu -->
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">

                                            <a href="{{ route('distributors.retailers.show', $currentRetailer->id) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                View Profile
                                            </a>

                                            <button type="button" onclick="confirmBlockMessages()"
                                                class="block w-full px-4 py-2 text-sm text-left {{ $isBlocked ? 'text-green-600' : 'text-red-600' }} hover:bg-gray-100">
                                                {{ $isBlocked ? 'Unblock Messages' : 'Block Messages' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages -->
                                <div id="messages-container" class="flex-1 p-4 space-y-4 overflow-y-auto">
                                    @if (!isset($hasExistingConversation) || !$hasExistingConversation)
                                        <!-- Show welcome message for new conversation -->
                                        <div class="flex flex-col items-center justify-center h-48">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-3 text-gray-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <p class="text-gray-500">Start a conversation with
                                                {{ $currentRetailer->first_name }}</p>
                                        </div>
                                    @else
                                        @forelse ($messages as $message)
                                            <div
                                                class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                                <div
                                                    class="{{ $message->sender_id == Auth::id() ? 'bg-green-100' : 'bg-white' }} rounded-lg p-3 shadow max-w-xs lg:max-w-md">
                                                    <p class="text-sm">{{ $message->message }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ $message->created_at->format('M d, h:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="flex flex-col items-center justify-center h-48">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-12 h-12 mb-3 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <p class="text-gray-500">Start a conversation with
                                                    {{ $currentRetailer->first_name }}</p>
                                            </div>
                                        @endforelse
                                    @endif
                                </div>

                                <!-- Message Input -->
                                <div class="p-4 bg-white border-t border-gray-200">
                                    <form id="message-form" class="flex space-x-2">
                                        <input type="hidden" name="receiver_id" value="{{ $currentRetailer->id }}">
                                        <input type="text" name="message" id="message-input"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                            placeholder="Type your message..." required>
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            Send
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- No selected retailer -->
                                <div class="flex flex-col items-center justify-center h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-4 text-gray-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <h3 class="mb-1 text-lg font-medium text-gray-900">No conversation selected</h3>
                                    <p class="text-gray-500">Select a retailer from the list or search for new
                                        retailers
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const messagesContainer = document.getElementById('messages-container');

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

                        messageInput.value = '';

                        const tempMessageDiv = document.createElement('div');
                        tempMessageDiv.className = 'flex justify-end';
                        tempMessageDiv.innerHTML = `
                    <div class="max-w-xs p-3 bg-green-100 rounded-lg shadow lg:max-w-md">
                        <p class="text-sm">${message}</p>
                        <p class="mt-1 text-xs text-gray-500">${new Date().toLocaleString()}</p>
                    </div>
                    `;

                        messagesContainer.appendChild(tempMessageDiv);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;

                        fetch("{{ route('distributors.messages.send') }}", {
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
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.status !== 'success') {
                                    console.error('Failed to send message:', data);
                                    tempMessageDiv.remove();
                                    alert('Failed to send message. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error sending message:', error);
                                tempMessageDiv.remove();
                                alert(
                                    'Failed to send message. Please check your connection and try again.'
                                );
                            });
                    });
                }

                // Setup Echo listener for real-time messages
                if (window.Echo) {

                    console.log('Setting up Echo listener in distributor messages view');

                    window.Echo.private(`chat.{{ Auth::id() }}`)
                        .listen('.message.sent', function(data) {
                            console.log('Received message event via Echo:', data);

                            if (messagesContainer) {
                                // Extract sender ID from different possible locations in the data object
                                const senderId = data.senderId || data.sender_id;
                                const currentRetailerId = {{ isset($currentRetailer) ? $currentRetailer->id : 0 }};

                                console.log('Current retailer ID:', currentRetailerId);
                                console.log('Message sender ID:', senderId);
                                console.log('Full data object:', JSON.stringify(data));

                                // Check if the message is from the current retailer
                                if (senderId == currentRetailerId) {
                                    // Add the message to the current chat with animation
                                    const messageDiv = document.createElement('div');
                                    messageDiv.className = 'flex justify-start';

                                    // Add animation class that slides in the message
                                    messageDiv.style.opacity = '0';
                                    messageDiv.style.transform = 'translateY(10px)';
                                    messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

                                    messageDiv.innerHTML = `
                <div class="max-w-xs p-3 bg-white rounded-lg shadow lg:max-w-md">
                    <p class="text-sm">${data.message}</p>
                    <p class="mt-1 text-xs text-gray-500">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                </div>
                `;

                                    messagesContainer.appendChild(messageDiv);

                                    // Force browser reflow to ensure the animation runs
                                    void messageDiv.offsetWidth;

                                    // Animate in
                                    messageDiv.style.opacity = '1';
                                    messageDiv.style.transform = 'translateY(0)';

                                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                                    // Mark as read
                                    fetch("{{ route('distributors.messages.mark-read') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            sender_id: senderId
                                        })
                                    }).catch(error => {
                                        console.error('Error marking message as read:', error);
                                    });
                                } else {
                                    // Retailer sidebar updating code remains the same
                                    const retailerElement = document.querySelector(
                                        `a[href*="retailer=${senderId}"]`);
                                    if (retailerElement) {
                                        // Add unread badge or highlight retailer
                                        const avatarContainer = retailerElement.querySelector('.relative');
                                        if (avatarContainer) {
                                            let unreadBadge = avatarContainer.querySelector('.absolute');
                                            if (!unreadBadge) {
                                                // Create new badge
                                                unreadBadge = document.createElement('span');
                                                unreadBadge.className =
                                                    'absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1';
                                                unreadBadge.textContent = '1';
                                                avatarContainer.appendChild(unreadBadge);
                                            } else {
                                                // Increment badge count
                                                const count = parseInt(unreadBadge.textContent || '0');
                                                unreadBadge.textContent = count + 1;
                                            }
                                        }

                                        // Move to top of list for new message
                                        const parentList = retailerElement.parentNode;
                                        if (parentList && parentList.firstChild !== retailerElement) {
                                            parentList.insertBefore(retailerElement, parentList.firstChild);
                                        }
                                    }
                                }
                            }
                        });
                    console.log('✅ Echo listener setup complete');
                } else {
                    console.error('❌ Laravel Echo is NOT initialized - check your app.js');
                }
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

            function confirmBlockMessages() {
                const retailerName =
                    "{{ isset($currentRetailer) ? $currentRetailer->first_name . ' ' . $currentRetailer->last_name : '' }}";
                const retailerId = {{ isset($currentRetailer) ? $currentRetailer->id : 0 }};
                const isBlocked = {{ $isBlocked ? 'true' : 'false' }};

                if (isBlocked) {
                    // Confirmation for unblocking
                    Swal.fire({
                        title: 'Unblock Messages',
                        html: `Are you sure you want to unblock messages from <strong>${retailerName}</strong>?<br><br>
            You will start receiving messages from this retailer again.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, unblock messages',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a form and submit it
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/messages/${retailerId}/block`; // <-- Fix the URL format

                            // Add CSRF token
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            form.appendChild(csrfToken);

                            document.body.appendChild(form);
                            form.submit();

                            // Redirect to messages index without the retailer parameter
                            setTimeout(function() {
                                window.location.href = "{{ route('distributors.messages.index') }}";
                            }, 300);
                        }
                    });
                } else {
                    // Confirmation for blocking
                    Swal.fire({
                        title: 'Block Messages',
                        html: `Are you sure you want to block messages from <strong>${retailerName}</strong>?<br><br>
            You will no longer receive messages from this retailer.`,
                        icon: 'warning',
                        input: 'text',
                        inputPlaceholder: 'Reason for blocking (optional)',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Block Messages',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a form and submit it
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/messages/${retailerId}/block`; // <-- Fix the URL format

                            // Add CSRF token
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            form.appendChild(csrfToken);

                            // Add reason if provided
                            if (result.value) {
                                const reasonInput = document.createElement('input');
                                reasonInput.type = 'hidden';
                                reasonInput.name = 'reason';
                                reasonInput.value = result.value;
                                form.appendChild(reasonInput);
                            }

                            document.body.appendChild(form);
                            form.submit();

                            // Important: Redirect to the messages index without any retailer parameter
                            setTimeout(function() {
                                window.location.href = "{{ route('distributors.messages.index') }}";
                            }, 300);
                        }
                    });
                }
            }
        </script>
    @endpush
</x-distributor-layout>
