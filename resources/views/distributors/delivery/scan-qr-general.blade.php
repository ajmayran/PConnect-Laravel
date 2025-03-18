<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Scan QR Code</h1>
            <a href="{{ route('distributors.delivery.index') }}"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                <i class="mr-1 fas fa-arrow-left"></i> Back to Deliveries
            </a>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-sm">
            <!-- Success message for completed delivery -->
            @if (session('success'))
                <div id="success-container" class="p-4 mb-6 text-green-800 bg-green-100 rounded-md">
                    <div class="flex items-center justify-between">
                        <p>{{ session('success') }}</p>
                        <button type="button" onclick="dismissSuccess()" class="text-green-600 hover:text-green-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Scan Again button after successful delivery -->
                    <div class="flex justify-center mt-4">
                        <button type="button" onclick="resetAndScanAgain()"
                            class="px-5 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Scan Next Delivery
                        </button>
                    </div>
                </div>
            @endif

            <!-- QR scanner - UPDATED FOR MOBILE -->
            <div class="mb-6" id="scanner-container" @if (session('success')) style="display: none;" @endif>
                <h3 class="mb-2 text-lg font-medium">Scan Order QR Code</h3>
                <div class="flex flex-col items-center space-y-4">
                    <!-- Responsive scanner container -->
                    <div id="reader-container" class="w-full max-w-md">
                        <!-- Dynamic size based on viewport width -->
                        <div id="reader" class="w-full aspect-square"></div>
                    </div>
                    <p class="text-sm text-gray-500">Position the QR code within the scanner frame</p>

                    <!-- Camera selector for mobile devices with multiple cameras -->
                    <div id="camera-selection" class="w-full max-w-md mt-2 text-center">
                        <button id="switch-camera-btn"
                            class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600"
                            style="display: none;">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4v16M8 4v16M13 4v16M18 4v16"></path>
                            </svg>
                            Switch Camera
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status message area -->
            <div id="status-message" class="hidden p-4 mb-4 rounded-md"></div>

            <!-- Scan Again button (hidden by default) -->
            <div id="scan-again-container" class="hidden mb-4 text-center">
                <button type="button" id="scan-again-btn"
                    class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Scan Again
                </button>
            </div>

            <!-- Manual entry form as fallback -->
            <form id="completionForm" action="{{ route('distributors.delivery.process-general-scan') }}" method="POST"
                class="mt-8" @if (session('success')) style="display: none;" @endif>
                @csrf
                <div class="mb-4">
                    <label for="qr_token" class="block text-sm font-medium text-gray-700">QR Token (if scanner
                        fails)</label>
                    <input type="text" name="qr_token" id="qr_token"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                </div>

                <div id="delivery-details" class="hidden p-4 mb-6 rounded-md bg-gray-50">
                    <h3 class="mb-2 text-lg font-medium">Delivery Details</h3>

                    <div id="order-info"> </div>
                </div>

                <div id="payment-options" class="hidden mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Payment Status</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_status" value="paid"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500" checked>
                            <span class="ml-2">Mark as Paid</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_status" value="unpaid"
                                class="text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2">Mark as Unpaid</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="submit-btn" disabled disabled onclick="confirmDeliveryCompletion()"
                        class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Complete Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const html5QrCode = new Html5Qrcode("reader");
                const qrForm = document.getElementById('completionForm');
                const qrTokenInput = document.getElementById('qr_token');
                const submitBtn = document.getElementById('submit-btn');
                const paymentOptions = document.getElementById('payment-options');
                const deliveryDetails = document.getElementById('delivery-details');
                const orderInfo = document.getElementById('order-info');
                const statusMessage = document.getElementById('status-message');
                const scanAgainBtn = document.getElementById('scan-again-btn');
                const scanAgainContainer = document.getElementById('scan-again-container');
                const switchCameraBtn = document.getElementById('switch-camera-btn');

                let scannerActive = false;
                let currentCamera = 'environment'; // Default to back camera
                let cameras = [];

                // Configure scanner options dynamically based on device
                function getScannerConfig() {
                    // Get container dimensions for responsive sizing
                    const readerElement = document.getElementById('reader');
                    const containerWidth = readerElement.clientWidth;

                    // Calculate qrbox size - use smaller box on mobile
                    const isMobile = window.innerWidth < 768;
                    let qrboxSize;

                    if (isMobile) {
                        // On mobile, use a smaller scan box (60% of container width)
                        qrboxSize = Math.min(Math.floor(containerWidth * 0.6), 200);
                    } else {
                        // On desktop, use a larger scan box
                        qrboxSize = Math.min(Math.floor(containerWidth * 0.8), 250);
                    }

                    return {
                        fps: 10,
                        qrbox: {
                            width: qrboxSize,
                            height: qrboxSize
                        },
                        aspectRatio: 1.0 // Square aspect ratio
                    };
                }

                // Check for available cameras
                function checkForCameras() {
                    Html5Qrcode.getCameras().then(devices => {
                        cameras = devices;
                        if (devices && devices.length > 1) {
                            switchCameraBtn.style.display = 'inline-block';

                            // Update button text based on current camera
                            switchCameraBtn.innerHTML = `
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16M8 4v16M13 4v16M18 4v16"></path>
                            </svg>
                            ${currentCamera === 'environment' ? 'Use Front Camera' : 'Use Back Camera'}
                        `;

                            // Add event listener for camera switch
                            switchCameraBtn.addEventListener('click', function() {
                                toggleCamera();
                            });
                        }
                    }).catch(err => {
                        console.error('Error getting cameras', err);
                    });
                }

                // Toggle between front and back cameras
                function toggleCamera() {
                    if (scannerActive) {
                        stopScanner();
                    }

                    currentCamera = currentCamera === 'environment' ? 'user' : 'environment';

                    // Update button text
                    switchCameraBtn.innerHTML = `
                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16M8 4v16M13 4v16M18 4v16"></path>
                    </svg>
                    ${currentCamera === 'environment' ? 'Use Front Camera' : 'Use Back Camera'}
                `;

                    // Restart scanner with new camera
                    startScanner();
                }

                // Function to start the scanner with appropriate configuration
                function startScanner() {
                    if (scannerActive) return; // Prevent multiple instances

                    // Reset UI elements
                    document.getElementById('reader').innerHTML = '';
                    scanAgainContainer.classList.add('hidden');

                    // Get dynamic configuration
                    const config = getScannerConfig();

                    // Start scanner
                    html5QrCode.start({
                            facingMode: currentCamera
                        },
                        config,
                        onScanSuccess
                    ).then(() => {
                        scannerActive = true;
                        fixScannerUI(); // Apply UI fixes for mobile
                    }).catch(error => {
                        console.error("Unable to start scanner", error);
                        showStatusMessage(
                            'Could not start camera scanner. Please ensure you\'ve granted camera permissions or enter the QR token manually.',
                            'warning');
                        scanAgainContainer.classList.remove('hidden'); // Show scan again button
                    });
                }

                // Apply CSS fixes to scanner UI for better mobile experience
                function fixScannerUI() {
                    setTimeout(() => {
                        // Fix video element sizing
                        const videoElement = document.getElementById('reader').querySelector('video');
                        if (videoElement) {
                            videoElement.style.objectFit = 'cover';
                            videoElement.style.borderRadius = '8px';
                        }

                        // Make scan region more visible
                        const scanRegion = document.querySelector('#reader__scan_region');
                        if (scanRegion) {
                            scanRegion.style.border = '3px solid #10b981';
                            scanRegion.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.3)';
                        }

                        // Hide unnecessary UI elements
                        const dashboardSection = document.querySelector('#reader__dashboard_section_swaplink');
                        if (dashboardSection) {
                            dashboardSection.style.display = 'none';
                        }
                    }, 500);
                }

                // Function to stop the scanner
                function stopScanner() {
                    if (!scannerActive) return;

                    html5QrCode.stop().then(() => {
                        scannerActive = false;
                    }).catch(error => {
                        console.error("Error stopping scanner:", error);
                    });
                }

                // Success callback when a QR is detected
                function onScanSuccess(decodedText) {
                    // Extract the token from the URL or use as is
                    const token = decodedText.includes('/') ? decodedText.split('/').pop() : decodedText;
                    qrTokenInput.value = token;

                    // Show success message
                    showStatusMessage('QR code detected! Verifying...', 'success');

                    // Stop the scanner after successful scan
                    stopScanner();
                    document.getElementById('reader').innerHTML =
                        '<div class="p-4 text-green-800 bg-green-100 rounded">QR code detected!</div>';

                    // Verify the QR code by getting details from server
                    verifyQrCode(token);
                }

                function verifyQrCode(token) {
                    // Using POST request with CSRF token for better security
                    fetch('/api/verify-qr-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                token: token
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show order details
                                showOrderDetails(data.order);
                                // Enable form submission
                                submitBtn.disabled = false;
                                paymentOptions.classList.remove('hidden');
                                // Update hidden input for delivery ID if needed for form submission
                                if (data.delivery && data.delivery.id) {
                                    const deliveryInput = document.createElement('input');
                                    deliveryInput.type = 'hidden';
                                    deliveryInput.name = 'delivery_id';
                                    deliveryInput.value = data.delivery.id;
                                    qrForm.appendChild(deliveryInput);
                                }
                            } else {
                                showStatusMessage(data.message || 'Invalid QR code or delivery not found.',
                                'error');
                                // Show scan again button when verification fails
                                scanAgainContainer.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error verifying QR code:', error);
                            showStatusMessage('Error verifying QR code. Please try again.', 'error');
                            // Show scan again button on error
                            scanAgainContainer.classList.remove('hidden');
                        });
                }

                function showOrderDetails(order) {
                    deliveryDetails.classList.remove('hidden');

                    let html = `
                    <p class="mb-2"><strong>Order ID:</strong> ${order.formatted_id}</p>
                    <p class="mb-2"><strong>Retailer:</strong> ${order.retailer_name}</p>
                    <p class="mb-2"><strong>Status:</strong> ${order.status}</p>
                    <p class="mb-2"><strong>Amount:</strong> ₱${order.total_amount}</p>
                `;

                    orderInfo.innerHTML = html;
                }

                function showStatusMessage(message, type) {
                    statusMessage.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'bg-yellow-100',
                        'text-green-800', 'text-red-800', 'text-yellow-800');

                    switch (type) {
                        case 'success':
                            statusMessage.classList.add('bg-green-100', 'text-green-800');
                            break;
                        case 'error':
                            statusMessage.classList.add('bg-red-100', 'text-red-800');
                            break;
                        case 'warning':
                            statusMessage.classList.add('bg-yellow-100', 'text-yellow-800');
                            break;
                    }

                    statusMessage.textContent = message;
                }

                // Manual token input
                qrTokenInput.addEventListener('input', function() {
                    if (this.value.length >= 6) { // Assuming tokens are at least 6 chars
                        verifyQrCode(this.value);
                    }
                });

                // Scan again button handler
                scanAgainBtn.addEventListener('click', function() {
                    resetAndScanAgain();
                });

                // Make the resetAndScanAgain function available globally
                window.resetAndScanAgain = function() {
                    // Show the scanner and form
                    document.getElementById('scanner-container').style.display = 'block';
                    document.getElementById('completionForm').style.display = 'block';

                    // Hide success message if present
                    const successContainer = document.getElementById('success-container');
                    if (successContainer) {
                        successContainer.style.display = 'none';
                    }

                    // Clear previous scan results
                    qrTokenInput.value = '';
                    submitBtn.disabled = true;
                    paymentOptions.classList.add('hidden');
                    deliveryDetails.classList.add('hidden');
                    orderInfo.innerHTML = '';
                    statusMessage.classList.add('hidden');

                    // Remove any delivery_id hidden input that might have been added
                    const oldDeliveryInput = qrForm.querySelector('input[name="delivery_id"]');
                    if (oldDeliveryInput) {
                        qrForm.removeChild(oldDeliveryInput);
                    }

                    // Restart scanner
                    startScanner();
                };

                window.confirmDeliveryCompletion = function() {
                    // Get payment status
                    const paymentStatus = document.querySelector('input[name="payment_status"]:checked').value;
                    const paymentLabel = paymentStatus === 'paid' ? 'Paid' : 'Unpaid';

                    // Get order info from the order-info div
                    const orderInfoEl = document.getElementById('order-info');
                    const orderID = orderInfoEl.querySelector('p:nth-child(1)')?.innerText || 'Order Details';
                    const retailerName = orderInfoEl.querySelector('p:nth-child(2)')?.innerText || '';
                    const orderAmount = orderInfoEl.querySelector('p:nth-child(4)')?.innerText || '';

                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Complete Delivery?',
                        html: `
                    <div class="text-left">
                        <p class="mb-2">${orderID}</p>
                        <p class="mb-2">${retailerName}</p>
                        <p class="mb-2">${orderAmount}</p>
                        <p class="mt-2"><strong>Payment Status:</strong> ${paymentLabel}</p>
                    </div>
                `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Complete It!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            document.getElementById('completionForm').submit();

                            // Show processing message
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Completing the delivery, please wait.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                allowEnterKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        }
                    });
                };

                // Function to dismiss success message
                window.dismissSuccess = function() {
                    const successContainer = document.getElementById('success-container');
                    if (successContainer) {
                        successContainer.style.display = 'none';
                    }

                    // Show scanner and form
                    document.getElementById('scanner-container').style.display = 'block';
                    document.getElementById('completionForm').style.display = 'block';

                    // Start scanner
                    startScanner();
                };

                // Handle window resize to adjust scanner configuration
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(function() {
                        if (scannerActive) {
                            stopScanner();
                            startScanner(); // Restart with new dimensions
                        }
                    }, 500);
                });

                // Initial setup
                checkForCameras();

                // Start scanning initially (unless we just completed a delivery)
                if (!document.getElementById('success-container') ||
                    document.getElementById('success-container').style.display === 'none') {
                    startScanner();
                }
            });
        </script>
        <style>
            /* Mobile-optimized styles for QR scanner */
            #reader {
                position: relative !important;
                width: 100% !important;
                aspect-ratio: 1 / 1 !important;
                min-height: 200px !important;
                max-height: 80vh !important;
                border-radius: 8px !important;
                overflow: hidden !important;
            }

            #reader video {
                object-fit: cover !important;
                width: 100% !important;
                height: 100% !important;
                border-radius: 8px !important;
            }

            /* Hide redundant elements added by the scanner library */
            #reader__dashboard_section_swaplink {
                display: none !important;
            }

            /* Make scan region visually better */
            #reader__scan_region {
                border: 3px solid #10b981 !important;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.3) !important;
            }

            /* Make sure content doesn't overflow on small screens */
            @media (max-width: 640px) {
                .container {
                    padding-left: 12px !important;
                    padding-right: 12px !important;
                }

                #reader-container {
                    width: 100% !important;
                    padding: 0 !important;
                }

                /* Enlarge buttons for better touch targets */
                button {
                    min-height: 44px !important;
                }
            }
        </style>
    @endpush
</x-distributor-layout>
