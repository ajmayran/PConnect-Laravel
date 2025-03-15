<x-distributor-layout>
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
                                                <div class="flex-shrink-0">
                                                    @if ($retailer->retailerProfile->profile_picture)
                                                        <img src="{{ Storage::url($retailer->retailerProfile->profile_picture) }}"
                                                            alt="{{ $retailer->retailerProfile->company_name }}"
                                                            class="object-cover w-10 h-10 rounded-full">
                                                    @else
                                                        <div
                                                            class="flex items-center justify-center w-10 h-10 text-gray-600 bg-gray-200 rounded-full">
                                                            {{ strtoupper(substr($retailer->retailerProfile->company_name, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $retailer->first_name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="py-4 text-center text-gray-500">No retailers available</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="flex flex-col w-3/4 bg-gray-50">
                            @if ($currentRetailer)
                                <!-- Chat Header -->
                                <div class="flex items-center p-4 bg-white border-b border-gray-200">
                                    <div class="flex-shrink-0">
                                        @if ($currentRetailer->retailerProfile->profile_picture)
                                            <img src="{{ Storage::url($currentRetailer->retailerProfile->profile_picture) }}"
                                                alt="{{ $currentRetailer->retailerProfile->profile_picture }}"
                                                class="object-cover w-10 h-10 rounded-full">
                                        @else
                                            <div
                                                class="flex items-center justify-center w-10 h-10 text-gray-600 bg-gray-200 rounded-full">
                                                {{ strtoupper(substr($currentRetailer->first_name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $currentRetailer->first_name }}
                                            {{ $currentRetailer->last_name }}</p>
                                    </div>
                                </div>

                                <!-- Messages -->
                                <div id="messages-container" class="flex-1 p-4 space-y-4 overflow-y-auto">
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
                                        <div class="py-8 text-center text-gray-500">
                                            Start a conversation with {{ $currentRetailer->first_name }}
                                        </div>
                                    @endforelse
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
                                <div class="flex items-center justify-center h-full">
                                    <p class="text-gray-500">Select a retailer to start messaging</p>
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

                // Set up Pusher
                const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                    cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth', // This should match the Laravel route
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    }
                });

                // Debug connection
                pusher.connection.bind('connected', function() {
                    console.log('Connected to Pusher successfully');
                });

                pusher.connection.bind('error', function(error) {
                    console.error('Pusher connection error:', error);
                });

                // Subscribe to the private channel
                const channel = pusher.subscribe('private-chat.{{ Auth::id() }}');

                // Debug channel subscription
                channel.bind('pusher:subscription_succeeded', function() {
                    console.log('Successfully subscribed to channel private-chat.{{ Auth::id() }}');
                });

                // Listen for new messages
                channel.bind('message.sent', function(data) {
                    console.log('Received message event:', data);

                    if (messagesContainer) {
                        const currentRetailerId = {{ $currentRetailer->id ?? 0 }};

                        console.log('Current retailer ID:', currentRetailerId);
                        console.log('Message sender ID:', data.senderId);

                        // Check if the message is from the current retailer
                        if (data.senderId == currentRetailerId) {
                            // Add the message to the current chat
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'flex justify-start';
                            messageDiv.innerHTML = `
                            <div class="max-w-xs p-3 bg-white rounded-lg shadow lg:max-w-md">
                                <p class="text-sm">${data.message}</p>
                                <p class="mt-1 text-xs text-gray-500">${data.time || new Date().toLocaleString()}</p>
                            </div>
                        `;

                            messagesContainer.appendChild(messageDiv);
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;

                            // Mark as read
                            fetch("{{ route('distributors.messages.mark-read') }}", {
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
                        } else {
                            // Highlight the retailer in the sidebar to indicate new message
                            const retailerElement = document.querySelector(
                                `a[href*="retailer=${data.senderId}"]`);
                            if (retailerElement) {
                                retailerElement.classList.add('bg-yellow-100', 'font-bold');
                                // Add a notification dot if not already there
                                if (!retailerElement.querySelector('.notification-dot')) {
                                    const nameElement = retailerElement.querySelector('.flex-1');
                                    if (nameElement) {
                                        const dot = document.createElement('span');
                                        dot.className = 'notification-dot w-3 h-3 bg-red-500 rounded-full ml-2';
                                        nameElement.appendChild(dot);
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-distributor-layout>
