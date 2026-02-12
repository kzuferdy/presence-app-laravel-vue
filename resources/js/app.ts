import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { initializeTheme } from './composables/useAppearance';

initializeTheme();

createApp(App)
    .use(router)
    .mount('#app');


// This will set light / dark mode on page load...
initializeTheme();
