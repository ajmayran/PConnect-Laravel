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
        transition: all 0.3s ease-in-out;
        width: 100%;
        margin-left: 0;
        overflow: hidden;
    }

    #arrow {
        transition: transform 0.3s ease;
    }
</style>

<body class="bg-gray-200" data-user-id="{{ Auth::id() ?? '' }}" data-user-type="distributor">

    <x-dist_navbar />
    <!-- Page Content -->
    
    <!-- Main Content - Full Width -->
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
        document.querySelector("#submenu")?.classList.toggle("hidden");
        document.querySelector("#arrow")?.classList.toggle("rotate-180");
    }
    
    // Only run dropdown if the elements exist
    if (document.querySelector("#submenu") && document.querySelector("#arrow")) {
        dropdown();
    }
</script>

</html>