<template>
    <div class="w-full px-8 md:px-32 lg:px-24">
        {{ auth }}
        <form @submit.prevent="onLogin" class="bg-white rounded-md shadow-2xl p-5">
            <h1 class="text-gray-800 font-bold text-2xl mb-8">¡Hola de nuevo!</h1>

            <div class="mb-4" v-if="messageError">
                <span class="text-red-600 text-sm">{{ messageError }}</span>
            </div>

            <div class="flex items-center border-2 mb-4 py-2 px-3 rounded-2xl">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                    />
                </svg>
                <input
                    id="email"
                    class="pl-2 w-full outline-none border-none"
                    type="email"
                    v-model="email"
                    placeholder="Email Address"
                    required
                />
            </div>
            <div class="flex items-center border-2 mb-6 py-2 px-3 rounded-2xl">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fillRule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clipRule="evenodd"
                    />
                </svg>
                <input
                    class="pl-2 w-full outline-none border-none"
                    type="password"
                    v-model="password"
                    id="password"
                    placeholder="Password"
                    required
                />
            </div>
            <button type="button"
                v-on:click="onLogin"
                class="block w-full bg-indigo-600 mt-5 py-2 rounded-2xl hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-500 text-white font-semibold mb-2"
                :class="{
                    'cursor-not-allowed bg-gray-500 hover:bg-gray-500': isDisabled || !formIsValid,
                    'cursor-pointer bg-indigo-600 hover:bg-indigo-700': !isDisabled
                }"
                :disabled="isDisabled || !formIsValid">
                Iniciar sesión
            </button>

            <hr class="my-6" />

            <div class="flex justify-between">
                <RouterLink :to="{ name: 'register' }"
                    class="text-sm ml-2 hover:text-blue-500 cursor-pointer hover:-translate-y-1 duration-500 transition-all"
                    >¿Aún no tienes una cuenta?
                </RouterLink>
            </div>
        </form>
    </div>
</template>

<script>

// import { useRouter } from 'vue-router';
import { mapMutations, mapState } from 'vuex';

export default {
    data() {
        return {
            email: '',
            password: '',
            isDisabled: false,
            messageError: null
        }
    },
    computed: {
        formIsValid() {
            return this.email !== '' && this.password !== '';
        },

        ...mapState([
            'auth'
        ])
    },
    methods: {
        onLogin() {
            this.isDisabled = true;

            this.axios.post('/login', {
                email: this.email,
                password: this.password
            }).then(response => {
                this.setAuth(response.data);
                localStorage.setItem('auth', JSON.stringify(response.data));
                this.isDisabled = false;

                this.$router.push({ name: 'notes' });
            })
            .catch(error => {
                console.log(error.response.data);
                this.messageError = error.response.data.message;
                this.isDisabled = false;
            });
        },

        ...mapMutations(['setAuth']),
    }
}

// const router = useRouter();
// const onLogin = () => {
//     console.log('login');

//     localStorage.setItem('userId', 'ABC-123');

//     const lastPath = localStorage.getItem('lastPath') ?? '/';

//     router.replace(lastPath);
//     // router.replace({
//     //     name: 'home',
//     // });
// }
</script>
