import { createApp } from 'vue';
import App from './App.vue';
import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import * as bootstrap from 'bootstrap';
import '@vueform/multiselect/themes/default.css';

import router from './router.js';
import { createI18n } from 'vue-i18n';
import apiClient from 'axios';

// import '@fortawesome/fontawesome-free/css/all.min.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons'; // Solid ikonkalar
import { fab } from '@fortawesome/free-brands-svg-icons'; // Brend ikonkalar
import { far } from '@fortawesome/free-regular-svg-icons';


// import './assets/main.css';
// import './assets/extended.css';

// import store from './store/index.js';
import en from './locales/en.json';
import ru from './locales/ru.json';


const messages = {
    en,
    ru,
};
const savedLang = localStorage.getItem('lang') || 'en';
const i18n = createI18n({
    locale: savedLang,
    fallbackLocale: 'en',
    messages,
});

const app = createApp(App);

app.config.globalProperties.$http = apiClient;

// Font Awesome ikonkalarini kutubxonaga qo‘shish
library.add(fas, fab, far);
// Font Awesome komponentini global registratsiya qilish
app.component('font-awesome-icon', FontAwesomeIcon);

app.use(i18n);
// app.use(store);
app.use(router);

app.mount('#app');