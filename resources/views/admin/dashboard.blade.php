<x-app-layout>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-dropdown-link :href="route('logout')"
            onclick="event.preventDefault();
            this.closest('form').submit();">
            {{ __('Log Out') }}
        </x-dropdown-link>
    </form>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Admin Dashboard</h1>
            </div>
            <div class="card-body">
                <h2>I AM AN ADMIN</h2>
                <p>Welcome, {{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
