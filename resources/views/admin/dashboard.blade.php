<x-app-layout>
    @if (session('success'))
        <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin_dash.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .circle-button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: gray;
            cursor: pointer;
        }

        .circle-button.active {
            background-color: white;
        }

        .tab-item.active {
            border-bottom: 2px solid rgb(38, 113, 38);
        }
    </style>
</head>
<x-navbar :user="Auth::user()" />

<Body>
    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
        <div class="fixed top-0 left-0 z-50 w-64 h-full p-4 transition-transform sidebar-menu"
            style="background-color: #abebc6;">
            <x-admin-sidebar />
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex justify-between mb-6">
                        <div>
                            <div class="mb-1 text-2xl font-semibold">529</div>
                            <div class="text-sm font-medium text-gray-400">Active Retailers</div>
                        </div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-full h-4 bg-gray-100 rounded-full">
                            <div class="h-full p-1 bg-green-500 rounded-full" style="width: 79%;">
                                <div class="w-2 h-2 ml-auto bg-white rounded-full"></div>
                            </div>
                        </div>
                        <span class="ml-4 text-sm font-medium text-gray-600">79%</span>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex justify-between mb-4">
                        <div>
                            <div class="flex items-center mb-1">
                                <div class="text-2xl font-semibold">2,420</div>
                                <div
                                    class="p-1 rounded bg-emerald-500/10 text-emerald-500 text-[12px] font-semibold leading-none ml-2">
                                    +89%</div>
                            </div>
                            <div class="text-sm font-medium text-gray-400">Visitors</div>
                        </div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <img src="https://placehold.co/32x32" alt=""
                            class="block object-cover w-8 h-8 rounded-full">
                        <img src="https://placehold.co/32x32" alt=""
                            class="block object-cover w-8 h-8 -ml-3 rounded-full">
                        <img src="https://placehold.co/32x32" alt=""
                            class="block object-cover w-8 h-8 -ml-3 rounded-full">
                        <img src="https://placehold.co/32x32" alt=""
                            class="block object-cover w-8 h-8 -ml-3 rounded-full">
                        <img src="https://placehold.co/32x32" alt=""
                            class="block object-cover w-8 h-8 -ml-3 rounded-full">
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex justify-between mb-6">
                        <div>
                            <div class="mb-1 text-2xl font-semibold"><span
                                    class="text-base font-normal text-gray-400 align-top"></span>2,345</div>
                            <div class="text-sm font-medium text-gray-400">Active Orders</div>
                        </div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-500 hover:text-blue-600">View details</a>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="font-medium">Transactions Overview</div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex items-center mb-4 order-tab">
                        <button type="button" data-tab="order" data-tab-page="active"
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-tl-md rounded-bl-md hover:text-gray-600 active">Active</button>
                        <button type="button" data-tab="order" data-tab-page="completed"
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 hover:text-gray-600">Completed</button>
                        <button type="button" data-tab="order" data-tab-page="Cancelled"
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-tr-md rounded-br-md hover:text-gray-600">Cancelled</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[540px]" data-tab-for="order" data-page="active">
                            <thead>
                                <tr>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                        Service</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Estimate</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Budget</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                        Status</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="w-full min-w-[540px] hidden" data-tab-for="order" data-page="completed">
                            <thead>
                                <tr>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                        Service</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Estimate</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Budget</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                1</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none">Completed</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                2</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none">Completed</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                3</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none">Completed</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                4</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none">Completed</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                5</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none">Completed</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="w-full min-w-[540px] hidden" data-tab-for="order" data-page="Cancelled">
                            <thead>
                                <tr>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                        Service</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Estimate</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Budget</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                1</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-rose-500/10 text-rose-500 font-medium text-[12px] leading-none">Cancelled</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                2</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-rose-500/10 text-rose-500 font-medium text-[12px] leading-none">Cancelled</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                3</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-rose-500/10 text-rose-500 font-medium text-[12px] leading-none">Cancelled</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                4</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-rose-500/10 text-rose-500 font-medium text-[12px] leading-none">Cancelled</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <div class="flex items-center">
                                            <img src="https://placehold.co/32x32" alt=""
                                                class="block object-cover w-8 h-8 rounded">
                                            <a href="#"
                                                class="ml-2 text-sm font-medium text-gray-600 truncate hover:text-blue-500">Order
                                                5</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">3 days</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span class="text-[13px] font-medium text-gray-400">$56</span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-b-gray-50">
                                        <span
                                            class="inline-block p-1 rounded bg-rose-500/10 text-rose-500 font-medium text-[12px] leading-none">Cancelled</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="font-medium">Manage Services</div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <form action="" class="flex items-center mb-4">
                        <div class="relative w-full mr-2">
                            <input type="text"
                                class="w-full py-2 pl-10 pr-4 text-sm border border-gray-100 rounded-md outline-none bg-gray-50 focus:border-blue-500"
                                placeholder="Search...">
                            <i class="absolute text-gray-400 -translate-y-1/2 ri-search-line top-1/2 left-4"></i>
                        </div>
                        <select
                            class="text-sm py-2 pl-4 pr-10 bg-gray-50 border border-gray-100 rounded-md focus:border-blue-500 outline-none appearance-none bg-select-arrow bg-no-repeat bg-[length:16px_16px] bg-[right_16px_center]">
                            <option value="">All</option>
                        </select>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[540px]">
                            <thead>
                                <tr>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                        Service</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Price</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Clicks</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5 lg:col-span-2">
                    <div class="flex items-start justify-between mb-4">
                        <div class="font-medium">Order Statistics</div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">10</div>
                                <span
                                    class="p-1 rounded text-[12px] font-semibold bg-blue-500/10 text-blue-500 leading-none ml-1">$80</span>
                            </div>
                            <span class="text-sm text-gray-400">Active</span>
                        </div>
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">50</div>
                                <span
                                    class="p-1 rounded text-[12px] font-semibold bg-emerald-500/10 stext-emerald-500 leading-none ml-1">+$469</span>
                            </div>
                            <span class="text-sm text-gray-400">Completed</span>
                        </div>
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">4</div>
                                <span
                                    class="p-1 rounded text-[12px] font-semibold bg-rose-500/10 text-rose-500 leading-none ml-1">-$130</span>
                            </div>
                            <span class="text-sm text-gray-400">Cancelled</span>
                        </div>
                    </div>
                    <div>
                        <canvas id="order-chart"></canvas>
                    </div>
                </div>
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="font-medium">User Engagement</div>
                        <div class="dropdown">
                            <button type="button"
                                class="text-gray-400 dropdown-toggle hover:text-gray-600"><iconify-icon
                                    icon="mdi:more-horiz" class="text-lg"></iconify-icon></button>
                            <ul
                                class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-gray-100 w-full max-w-[140px]">
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Goto</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-500 hover:bg-gray-50">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[460px]">
                            <thead>
                                <tr>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tl-md rounded-bl-md">
                                        User</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left">
                                        Rating</th>
                                    <th
                                        class="text-[12px] uppercase tracking-wide font-medium text-gray-400 py-2 px-4 bg-gray-50 text-left rounded-tr-md rounded-br-md">
                                        Review</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</Body>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</html>
</x-app-layout>