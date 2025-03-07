<header class="sticky top-0 z-10 bg-white border-b border-gray-300 drop-shadow-sm">
    <div class="container flex items-center justify-between px-4 py-4 mx-auto">
      <div class="flex items-center">
        <img alt="PConnect Logo" class="w-8 h-8" src="{{('img/Pconnect Logo.png')}}" />
        <span class="ml-2 text-xl font-bold">
          PConnect
        </span>
      </div>
      <div class="flex items-center space-x-2">
        <span><img alt="Profile Picture" class="w-10 h-10 border border-gray-100 rounded-full" src="../../resources/img/Distrubutors/zamba.jpg" /></span>
        <span class="p-1 mb-1 font-sans"></span>

        <!-- Notification Button -->
        <div class="relative">
          <button class="p-1 rounded-lg hover:bg-gray-100" id="notificationButton">
            <iconify-icon icon="mdi:notifications" class="text-xl text-green-500"></iconify-icon>
          </button>
          <div id="notificationDropdown" class="absolute right-0 z-10 hidden w-64 p-2 bg-white border border-gray-200 rounded-lg shadow-lg">
            <div class="flex justify-between p-2">
              <span class="text-lg font-semibold">Notifications</span>
              <iconify-icon icon="proicons:more" class="mt-1 text-xl text-gray-500"></iconify-icon>
            </div>
            <hr class="border-gray-300 shadow-sm" />
            <ul class="py-2 mb-2">
              <li class="px-4 py-2 font-sans text-sm hover:bg-gray-100">Notification 1</li>
              <li class="px-4 py-2 font-sans text-sm hover:bg-gray-100">Notification 2</li>
              <li class="px-4 py-2 font-sans text-sm hover:bg-gray-100">Notification 3</li>
            </ul>
            <button class="w-full px-1 py-2 mx-auto text-white bg-green-500 rounded-md text-[12px] hover:bg-green-400">See previous notifications</button>
          </div>
        </div>

        <!-- Account Button -->
        <div class="relative">
          <button class="p-1 rounded-lg hover:bg-gray-100" id="accountButton">
            <iconify-icon icon="mdi:account" class="text-xl text-green-500"></iconify-icon>
          </button>
          <div id="accountPopper" class="absolute right-0 z-10 hidden w-64 bg-white border border-gray-200 rounded-lg shadow-lg">
            <div class="flex items-center p-4 mx-2 my-2 border border-gray-200 rounded-lg shadow-sm">
              <img alt="Profile Picture" class="w-10 h-10 border border-gray-100 rounded-full" src="../../resources/img/Distrubutors/zamba.jpg" />
              <span class="ml-2 text-sm font-normal"></span>
            </div>
            <ul class="py-2">
              <a href="./dist_settings.php">
                <li class="px-4 py-2 font-sans text-sm hover:bg-gray-100">Settings</li>
              </a>
              <a href="./dist_logout.php">
                <li class="px-4 py-2 font-sans text-sm hover:bg-gray-100">Logout</li>
              </a>
            </ul>
          </div>
        </div>
      </div>
    </div>
    </div>
  </header>