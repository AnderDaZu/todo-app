<template>
    <div class="flex flex-col h-screen bg-main">
        <header class="flex items-center min-h-16 px-4 border-b border-gray-300 sm:h-16 md:px-6 lg:px-8">
            <div>
                <RouterLink :to="{ name: 'dashboard' }">Inicio</RouterLink>
            </div>
            <nav class="ml-auto">
                <!-- para resaltar la ruta actual se agrega exact-active-class="clases" a cada elemento -->
                <div v-if="auth" class="space-x-4 flex items-center sm:space-x-6 h-10">
                    <RouterLink :to="{ name: 'notes' }"> Notas </RouterLink>
                    <button @click="logout" class="btn-logout">Cerrar sesión</button>
                </div>
                <div v-else class="space-x-4 flex items-center sm:space-x-6 h-10">
                    <RouterLink to="/auth"> Iniciar sesión </RouterLink>
                    <RouterLink :to="{ name: 'register'} "> Registrarse </RouterLink>
                </div>
            </nav>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center">
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
            <!-- <RouterView /> -->
        </main>
        <!-- Fin Main -->

        <!-- Footer -->
        <footer class="flex items-center min-h-14 px-4 border-t border-gray-300 sm:h-16 md:px-6 lg:px-8">
            <p class="flex-1 text-sm text-gray-500 text-center">
                © {{ new Date().getFullYear() }} ToDo App. Derechos reservados
            </p>
        </footer>
        <!-- Fin Footer -->
    </div>
</template>

<script>

import { RouterLink } from 'vue-router';
import { mapActions, mapState } from 'vuex';

export default {
    created() {
        this.setAuth();
    },
    methods: {
        ...mapActions([
            'setAuth',
            'logout'
        ]),
    },
    computed: {
        ...mapState([
            'auth'
        ])
    }
}

</script>

<style>
    .bg-main {
        background-color: #f1f1f1;
    }
    .btn-logout {
        @apply bg-red-500 hover:bg-red-600 hover:text-white text-gray-50 py-1.5 px-3 rounded;
    }
</style>
