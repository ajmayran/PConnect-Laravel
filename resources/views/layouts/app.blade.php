{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/components/app-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>PConnect</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>
    <style>
        .notification-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
        }
    </style>
    <script>
        window.PusherDebug = true; // Enable Pusher debugging
    </script>
</head>


<body class="font-sans antialiased" data-user-id="{{ Auth::id() ?? '' }}" data-user-type="retailer">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-200">
        @stack('scripts')
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
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
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
</body>

</html>