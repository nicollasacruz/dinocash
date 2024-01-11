import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import translationMixin from './translation.js';
import VueTheMask from 'vue-the-mask';

const appName = import.meta.env.VITE_APP_NAME || 'Dinobet';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .mixin(translationMixin)
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(VueTheMask)
            .mount(el);
    },
    progress: {
        color: '#FF0000',
    },
});
