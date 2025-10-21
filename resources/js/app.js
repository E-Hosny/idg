/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { route } from 'ziggy-js';
import '../css/app.css';
import translations from './translations.js';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

// Make route function available globally
window.route = route

createInertiaApp({
  title: (title) => title ? `${title} - IDG` : 'IDG',
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
    
    // Make route function available in components
    app.config.globalProperties.$route = route
    // Simple i18n helper using resources/js/translations.js
    app.config.globalProperties.$t = (key) => {
      try {
        const htmlLang = (document?.documentElement?.lang || 'en').split('-')[0];
        const locale = ['ar', 'en'].includes(htmlLang) ? htmlLang : 'en';
        const dict = translations?.[locale] || {};
        return dict[key] || key;
      } catch (e) {
        return key;
      }
    }
    
    return app.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
});
