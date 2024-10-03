import './bootstrap';

import { createApp } from 'vue';
import App from './modules/App.vue';

import axios from 'axios';
import VueAxios from 'vue-axios';

import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';

import store from './store';

axios.defaults.withCredentials = true;

const apiUrl = process.env.APP_ENV === 'local'
    ? 'http://todo-app.test/api'
    : 'https://todo-app-prod.up.railway.app/api';

axios.defaults.baseURL = apiUrl;

// Crear instancia del router
const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach((to, from, next) => {
    const protectedRoute = to.matched.some(record => record.meta.requiresAuth);

    // validación para cuando un usuario no se ha autenticado
    if (protectedRoute && !localStorage.getItem('auth')) {
        next({ name: 'login' });
    } else {

        next();
    }

});

// Crear instancia de la aplicación Vue
const app = createApp(App);

// Usar el store de Vuex
app.use(store);

// Usar plugins de Vue 3
app.use(router);
app.use(VueAxios, axios);

// Montar la aplicación
app.mount('#app');
