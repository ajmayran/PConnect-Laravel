<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.userId = {{ auth()->id() }};
        window.pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
        window.pusherAppCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
    </script>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>PConnect</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .notification-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
        }
    </style>
</head>
<style>
    body {
        font-family: 'Lexend', sans-serif;
    }

    #main-content {
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        width: calc(100% - 300px);
        margin-left: 300px;
        overflow: hidden;
    }

    #sidebar {
        transition: transform 0.3s ease-in-out;
        width: 300px;
    }

    @media (max-width: 1024px) {
        #sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 50;
            width: 300px;
        }

        #main-content {
            width: 100%;
            margin-left: 0;
        }
    }

    #arrow {
        transition: transform 0.3s ease;
    }
</style>

<body class="bg-gray-200" data-user-id="{{ Auth::id() ?? '' }}" data-user-type="distributor">



    <x-dist_navbar />
    <!-- Page Content -->
    <span class="absolute text-4xl text-white cursor-pointer top-5 left-4 z-[50]" onclick="toggleSidebar()">
        <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
    </span>

    <!-- Sidebar -->
    <x-distributor-sidebar />

    <!-- Main Content -->
    <div id="main-content" class="min-h-screen p-4 transition-all duration-300 ease-in-out">
        {{ $slot }}
    </div>



    @stack('scripts')

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
</body>

<script src="{{ asset('js/distributornotif-utils.js') }}"></script>
<script>
    function dropdown() {
        document.querySelector("#submenu").classList.toggle("hidden");
        document.querySelector("#arrow").classList.toggle("rotate-180");
    }
    dropdown();

    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("main-content");

        if (window.innerWidth >= 1024) {
            if (sidebar.classList.contains("lg:-translate-x-full")) {
                sidebar.classList.remove("lg:-translate-x-full");
                mainContent.style.marginLeft = "300px";
                mainContent.style.width = "calc(100% - 300px)";
            } else {
                sidebar.classList.add("lg:-translate-x-full");
                mainContent.style.marginLeft = "0";
                mainContent.style.width = "100%";
            }
        } else {
            if (sidebar.style.transform === "translateX(0%)") {
                sidebar.style.transform = "translateX(-100%)";
            } else {
                sidebar.style.transform = "translateX(0%)";
            }
        }
    }
</script>

</html>
