import './bootstrap';
import 'flowbite';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});