@props(['user'])

@if($user && $user->user_type === 'retailer' && !$user->profile_completed)
    <div id="profileCompletionAlert" class="fixed inset-x-0 top-0 z-[200] py-3 bg-yellow-100 bg-opacity-70 border-b border-yellow-200 backdrop-blur-sm shadow-md">
        <div class="container flex items-center justify-between px-4 mx-auto">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="font-medium text-yellow-800">Your profile is incomplete. <a href="{{ route('retailers.profile.edit') }}" class="font-bold text-yellow-700 underline hover:text-yellow-900">Complete your profile now</a> to access all features.</span>
            </div>
            <button id="closeProfileAlert" class="text-yellow-600 transition-colors hover:text-yellow-900 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileAlert = document.getElementById('profileCompletionAlert');
            const closeButton = document.getElementById('closeProfileAlert');
            
            // Instead of localStorage, use sessionStorage to track just for current browsing session
            const alertKey = 'profileAlertHiddenTime';
            const pageViewKey = 'profileAlertPageViews';
            
            // Track page views in session
            let pageViews = parseInt(sessionStorage.getItem(pageViewKey) || '0');
            pageViews++;
            sessionStorage.setItem(pageViewKey, pageViews.toString());
            
            // Check if this is a new page navigation
            const isNewPageNavigation = pageViews > 1;
            
            // Get the last time the alert was hidden
            const lastHiddenTime = sessionStorage.getItem(alertKey);
            const currentTime = new Date().getTime();
            
            // If this is a new page navigation, always show the alert
            // Otherwise, check if 30 seconds have passed since last hiding
            if (isNewPageNavigation) {
                showAlert();
            } else if (lastHiddenTime && currentTime - parseInt(lastHiddenTime) < 30000) {
                profileAlert.classList.add('hidden');
                
                // Set timer to show alert again after remaining time
                const remainingTime = 30000 - (currentTime - parseInt(lastHiddenTime));
                setTimeout(showAlert, remainingTime);
            }
            
            // Function to show the alert
            function showAlert() {
                profileAlert.classList.remove('hidden');
                sessionStorage.removeItem(alertKey);
            }
            
            // Function to hide the alert
            function hideAlert() {
                profileAlert.classList.add('hidden');
                sessionStorage.setItem(alertKey, new Date().getTime().toString());
                
                // Set timer to show alert again after 30 seconds
                setTimeout(showAlert, 30000);
            }
            
            // Event listener for the close button
            closeButton.addEventListener('click', function() {
                hideAlert();
            });
        });
    </script>
@endif