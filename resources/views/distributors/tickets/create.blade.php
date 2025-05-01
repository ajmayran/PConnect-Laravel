<x-app-layout>
    <div class="container px-4 py-8 mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-gray-800">
                <h1 class="text-2xl font-bold text-white">Create Ticket</h1>
            </div>
            <div class="p-6">
                <form id="ticketForm" method="POST" action="{{ route('distributors.tickets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="step1">
                        <div class="mb-4">
                            <label for="issue_type" class="block text-sm font-medium text-gray-700">What can we help with today?</label>
                            <select name="issue_type" id="issue_type" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                                <option value="">Select an option</option>
                                <option value="General Question">General Question</option>
                                <option value="Bug Report">Bug Report</option>
                                <option value="Payment Issues">Payment Issues</option>
                                <option value="Account Problems">Account Problems</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div id="other_issue" class="mb-4 hidden">
                            <label for="other_issue_text" class="block text-sm font-medium text-gray-700">Please explain</label>
                            <textarea name="other_issue_text" id="other_issue_text" rows="3" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="showStep2()" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">Continue</button>
                        </div>
                    </div>
                    <div id="step2" class="hidden">
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                        </div>
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="content" id="content" rows="5" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Upload Screenshot/Image (jpg, png, jpeg)</label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">Submit</button>
                            <button type="button" onclick="showStep1()" class="px-4 py-2 ml-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Back</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('issue_type').addEventListener('change', function() {
            if (this.value === 'Others') {
                document.getElementById('other_issue').classList.remove('hidden');
            } else {
                document.getElementById('other_issue').classList.add('hidden');
            }
        });

        function showStep2() {
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        }

        function showStep1() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
        }
    </script>
</x-app-layout>