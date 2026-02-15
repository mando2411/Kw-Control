import './bootstrap';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';


window.Alpine = Alpine;

Alpine.start();
window.Pusher = require('pusher-js');

// Configure Echo with Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

