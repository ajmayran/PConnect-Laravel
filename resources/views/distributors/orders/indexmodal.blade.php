<div id="orderModal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
        <!-- Modal Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
            <h2 class="text-xl font-bold text-gray-800" id="modalTitle">Order Details</h2>
            <button onclick="closeModal()"
                class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div id="modalContent" class="p-6">
            <!-- Modal Content will be loaded dynamically -->
            <div class="flex items-center justify-center p-12">
                <div class="w-12 h-12 border-t-2 border-b-2 border-green-500 rounded-full animate-spin"></div>
            </div>
        </div>

        <!-- Modal Footer with Accept, Reject, and Close buttons -->
        <div class="sticky bottom-0 flex justify-end gap-4 p-4 bg-white border-t">
            <div id="actionButtons" class="hidden">
                <button onclick="acceptOrder()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    Accept
                </button>
                <button onclick="openRejectModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                    Reject
                </button>
            </div>
            <!-- Add QR Code Button -->
            <a id="qrCodeButton" href="#" class="hidden px-4 py-2 font-medium text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                QR Code
            </a>
            <button id="editOrderButton" onclick="openEditOrderModal()" 
                class="hidden px-4 py-2 font-medium text-white transition-colors bg-yellow-500 rounded-lg hover:bg-yellow-600">
                Edit Order
            </button>
            <button onclick="closeModal()"
                class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                Close
            </button>
        </div>
    </div>
</div>

    <!-- Reject Reason Modal -->
    <div id="rejectModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">Reject Order</h2>
            </div>
            <div class="p-4">
                <p class="mb-2 text-gray-700">Select a rejection reason:</p>
                <div>
                    <label class="flex items-center mb-2">
                        <input type="radio" name="reject_reason_option" value="Out of stock" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Out of stock
                    </label>
                    <label class="flex items-center mb-2">
                        <input type="radio" name="reject_reason_option" value="Price mismatch" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Price mismatch
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="reject_reason_option" value="Other" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Other
                    </label>
                </div>
                <textarea id="rejectOtherReason" class="hidden w-full p-2 mt-2 border rounded"
                    placeholder="Enter custom rejection reason..."></textarea>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t">
                <button onclick="submitRejectOrder()"
                    class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                    Submit
                </button>
                <button onclick="closeRejectModal()"
                    class="px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div id="batchQrModal"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-2/3">
        <!-- Modal Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
            <h2 class="text-xl font-bold text-gray-800">Generate Batch QR Codes</h2>
            <button onclick="closeBatchQrModal()"
                class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <div class="mb-4">
                <p class="mb-2 text-gray-700">Select the orders you want to generate QR codes for:</p>
                <div class="overflow-y-auto max-h-[40vh] border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr>
                                <th class="w-10 px-4 py-3">
                                    <input type="checkbox" id="selectAll"
                                        class="border-gray-300 rounded cursor-pointer"
                                        onchange="toggleAllCheckboxes()">
                                </th>
                                <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Order
                                    ID</th>
                                <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                    Retailer</th>
                                <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="batchOrdersList">
                            <!-- Orders will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <p class="text-sm text-gray-600"><span id="selectedCount">0</span> orders selected</p>
                <div class="space-x-2">
                    <button onclick="generateSelectedQrCodes()" id="generateQrButton" disabled
                        class="px-4 py-2 font-medium text-white transition-colors bg-blue-500 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-blue-600">
                        Generate QR Codes
                    </button>
                    <button onclick="closeBatchQrModal()"
                        class="px-4 py-2 font-medium text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-2xl bg-white rounded-lg shadow-xl">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">Edit Order</h2>
            </div>
            <div class="p-4">
                <form id="editOrderForm">
                    <div id="editOrderItems" class="space-y-4">
                        <!-- Order items will be dynamically populated here -->
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="submitEditOrder()"
                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditOrderModal()"
                            class="px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>