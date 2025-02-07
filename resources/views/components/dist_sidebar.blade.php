<aside class="w-1/4 min-h-screen mt-0.5 bg-white sidebar-menu" id="sidebar">
    <ul class="m-10 ml-10 space-y-2">
        <li class="group <?php echo ($currentPage == 'dist_dashboard.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_dashboard.php">
                <iconify-icon icon="mdi:home" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Dashboard</span>
            </a>
        </li>

        <li class="group <?php echo ($currentPage == 'dist_orders.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_orders.php">
                <iconify-icon icon="material-symbols-light:sell" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">My Orders</span>
            </a>
        </li>

        <li class="group <?php echo ($currentPage == 'dist_return.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_return.php">
                <iconify-icon icon="ph:key-return-fill" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Return | Refund</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_cancellation.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_cancellation.php"><iconify-icon icon="basil:cancel-solid" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Cancellation</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_delivery.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_delivery.php"><iconify-icon icon="mdi:truck-delivery" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Delivery</span>
            </a>
        </li>
        <hr class="border-gray-300 shadow-sm" />
        <li class="group <?php echo ($currentPage == 'dist_products.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_products.php"><iconify-icon icon="dashicons:products" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">My Products</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_inventory.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_inventory.php"><iconify-icon icon="ic:baseline-inventory-2" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Inventory</span>
            </a>
        </li>
        <hr class="border-gray-300 shadow-sm" />
        <li class="group <?php echo ($currentPage == 'dist_messages.php') ? 'active' : ''; ?> ">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_messages.php"><iconify-icon icon="ant-design:message-filled" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Messages</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_blocking.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_blocking.php"><iconify-icon icon="material-symbols:block" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Blocking</span>
            </a>
        </li>
        <hr class="border-gray-300 shadow-sm" />
        <li class="group <?php echo ($currentPage == 'dist_insights.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_insights.php"><iconify-icon icon="gg:insights" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Business Insights</span>
            </a>
        </li>
        <hr class="border-gray-300 shadow-sm" />

        <li class="group <?php echo ($currentPage == 'dist_voucher.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_voucher.php"><iconify-icon icon="mdi:voucher" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Voucher</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_settings.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_settings.php"><iconify-icon icon="material-symbols:settings" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Shop Settings</span>
            </a>
        </li>
        <li class="group <?php echo ($currentPage == 'dist_staff.php') ? 'active' : ''; ?>">
            <a class="flex items-center px-4 py-1 hover:bg-green-300 hover:text-gray-100" href="./dist_staff.php"><iconify-icon icon="material-symbols:settings" class="ml-10 mr-1 text-xl text-green-500 icon"></iconify-icon>
                <span class="ml-2 font-normal">Manage Staff</span>
            </a>
        </li>
    </ul>
</aside>