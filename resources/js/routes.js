
export const routes = [
    {
        path: '/home',
        name: 'home',
        redirect: { name: 'dashboard' },
        component: () => import('./modules/App.vue'),
    },
    {
        path: '/',
        name: 'dashboard',
        component: () => import('./modules/common/pages/DashboardPage.vue'),
    },

    // Notes
    {
        path: '/notes',
        name: 'notes',
        meta: {
            requiresAuth: true
        },
        component: () => import('./modules/notes/layouts/NoteLayout.vue'),
        children: [
            {
                path: '/',
                name: 'showNotes',
                component: () => import('./modules/notes/pages/ShowNotePage.vue'),
            },
            {
                path: 'create',
                name: 'createNote',
                component: () => import('./modules/notes/pages/CreateNotePage.vue'),
            },
            {
                path: 'edit/:id',
                name: 'editNote',
                component: () => import('./modules/notes/pages/EditNotePage.vue'),
            },
            {
                path: 'show/:id',
                name: 'showNote',
                component: () => import('./modules/notes/pages/ShowNotePage.vue'),
            }
        ]
    },

    // Auth
    {
        path: '/auth',
        redirect: { name: 'login' },
        component: () => import('./modules/auth/layouts/AuthLayout.vue'),
        beforeEnter: (to, from, next) => {
            if (localStorage.getItem('auth')) {
                next({ name: 'notes' });
            } else {
                next();
            }
        },
        children: [
            {
                path: 'login',
                name: 'login',
                component: () => import('./modules/auth/pages/LoginPage.vue'),

            },
            {
                path: 'register',
                name: 'register',
                component: () => import('./modules/auth/pages/RegisterPage.vue'),
            },
        ]
    },

    {
        path: '/:pathMatch(.*)*',
        name: 'notFound',
        component: () => import('./modules/common/pages/404NotFoundPage.vue'),
    }
]
