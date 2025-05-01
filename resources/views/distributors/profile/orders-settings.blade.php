<x-distributor-layout>
    <div class="py-2">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="{{ route('distributors.profile.edit') }}"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                            Company Profile</a>
                    </li>
                    <li class="mr-2">
                        <a href="{{ route('distributors.profile.orders-settings') }}"
                            class="inline-block p-4 text-green-600 border-b-2 border-green-500 rounded-t-lg active"
                            aria-current="page">Orders</a>
                    </li>
                </ul>
            </div>
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                        {{ __('Order Settings') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                        {{ __('Configure your order acceptance settings and cut-off time.') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('distributors.profile.update.orders') }}" class="mt-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2">
                        <!-- Cut-off Time -->
                        <div>
                            <label for="cut_off_time" class="block text-sm font-medium text-gray-700">
                                Order Cut-off Time
                            </label>
                            <div class="flex flex-col gap-2 mt-1">
                                <!-- Cut-off Time Selection -->
                                <div id="cutOffTimeSelector" class="flex items-center gap-2 mb-2">
                                    <select id="cut_off_hour" name="cut_off_hour"
                                        class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                                {{ old('cut_off_hour', isset(Auth::user()->distributor->cut_off_time) ? date('h', strtotime(Auth::user()->distributor->cut_off_time)) : '') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                            </option>
                                        @endfor
                                    </select>
                                    <span class="text-xl">:</span>
                                    <select id="cut_off_minute" name="cut_off_minute"
                                        class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                        @for ($i = 0; $i < 60; $i += 5)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                                {{ old('cut_off_minute', isset(Auth::user()->distributor->cut_off_time) ? date('i', strtotime(Auth::user()->distributor->cut_off_time)) : '') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                            </option>
                                        @endfor
                                    </select>
                                    <select id="cut_off_period" name="cut_off_period"
                                        class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                        <option value="AM"
                                            {{ old('cut_off_period', isset(Auth::user()->distributor->cut_off_time) ? date('A', strtotime(Auth::user()->distributor->cut_off_time)) : '') == 'AM' ? 'selected' : '' }}>
                                            AM</option>
                                        <option value="PM"
                                            {{ old('cut_off_period', isset(Auth::user()->distributor->cut_off_time) ? date('A', strtotime(Auth::user()->distributor->cut_off_time)) : '') == 'PM' ? 'selected' : '' }}>
                                            PM</option>
                                    </select>
                                    <input type="hidden" id="cut_off_time" name="cut_off_time">
                                </div>

                                <!-- Remove Cut-off Time Toggle -->
                                <div class="flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="remove_cut_off_time" name="remove_cut_off_time"
                                            class="sr-only peer"
                                            {{ Auth::user()->distributor->cut_off_time ? '' : 'checked' }}>
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full 
                                            peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white 
                                            after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">Remove cut-off time</span>
                                    </label>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                The daily cut-off time after which new orders will not be processed until the next
                                business day. Orders placed after this time will be queued for processing the following
                                day.
                                If removed, orders can be placed at any time.
                            </p>
                        </div>

                        <!-- Order Acceptance Toggle -->
                        <div>
                            <label for="accepting_orders" class="block text-sm font-medium text-gray-700">
                                Accept New Orders
                            </label>
                            <div class="flex items-center gap-3 mt-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="accepting_orders" name="accepting_orders" value="1"
                                        class="sr-only peer"
                                        {{ Auth::user()->distributor->accepting_orders ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 
                                        peer-focus:ring-blue-300 rounded-full peer 
                                        peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                                        peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                                        after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                                        after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                    </div>
                                </label>
                                <div class="text-sm font-medium" id="orderStatusLabel">
                                    {{ Auth::user()->distributor->accepting_orders ? 'Currently accepting orders' : 'Not accepting orders' }}
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Toggle this setting to start or stop accepting new orders. When turned off, retailers
                                cannot place new orders with your shop.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-primary-button type="submit">{{ __('Save Settings') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Time input handling
            const form = document.querySelector('form');
            const removeCutOffTimeCheckbox = document.getElementById('remove_cut_off_time');
            const cutOffTimeSelector = document.getElementById('cutOffTimeSelector');

            // Initial state
            updateCutOffTimeDisplay();

            // Toggle cut-off time selector visibility based on checkbox
            removeCutOffTimeCheckbox.addEventListener('change', updateCutOffTimeDisplay);

            function updateCutOffTimeDisplay() {
                const isRemoved = removeCutOffTimeCheckbox.checked;
                cutOffTimeSelector.style.opacity = isRemoved ? '0.5' : '1';
                cutOffTimeSelector.style.pointerEvents = isRemoved ? 'none' : 'auto';
            }

            form.addEventListener('submit', function(e) {
                // Prevent default submission temporarily
                e.preventDefault();

                // Handle cut-off time value
                if (!removeCutOffTimeCheckbox.checked) {
                    const hour = document.getElementById('cut_off_hour').value;
                    const minute = document.getElementById('cut_off_minute').value;
                    const period = document.getElementById('cut_off_period').value;

                    // Convert to 24-hour format for the hidden input
                    let hourValue = parseInt(hour);
                    if (period === 'PM' && hourValue !== 12) hourValue += 12;
                    if (period === 'AM' && hourValue === 12) hourValue = 0;

                    const timeValue = `${String(hourValue).padStart(2, '0')}:${minute}`;
                    document.getElementById('cut_off_time').value = timeValue;

                    console.log('Setting cut_off_time to:', timeValue);
                } else {
                    // Set to empty if remove is checked
                    document.getElementById('cut_off_time').value = '';
                    console.log('Removing cut_off_time');
                }

                // Continue with form submission
                form.submit();
            });

            // Toggle order acceptance status message
            const acceptingOrdersToggle = document.getElementById('accepting_orders');
            const statusLabel = document.getElementById('orderStatusLabel');

            if (acceptingOrdersToggle && statusLabel) {
                acceptingOrdersToggle.addEventListener('change', function() {
                    statusLabel.textContent = this.checked ? 'Currently accepting orders' :
                        'Not accepting orders';
                });
            }
        });

        // Show success message when settings are saved
        @if (session('status') === 'orders-settings-updated')
            Swal.fire({
                icon: 'success',
                title: 'Settings Saved!',
                text: 'Your order settings have been updated successfully.',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
</x-distributor-layout>
