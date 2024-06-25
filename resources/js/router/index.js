import { createRouter, createWebHistory } from "vue-router";
import store from "@/store";

const routes = [
    {
        path: "/",
        component: () => import("./../components/Layout.vue"),
        children: [
            {
                path: "",
                name: "home",
                component: () => import("./../Pages/Home.vue"),
            },
            {
                path: "chat",
                name: "chat",
                component: () => import("./../Pages/Chat.vue"),
            },
            {
                path: "profile",
                name: "profile",
                component: () => import("./../Pages/Profile.vue"),
            },
        ],
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/login",
        name: "login",
        component: () => import("./../Pages/Auth/Login.vue"),
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("./../Pages/Auth/Register.vue"),
        meta: {
            requiresAuth: false,
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    linkActiveClass: "border-gray-800 bg-gray-300 text-black",
    linkExactActiveClass: "border-gray-800 bg-gray-300 text-black",
});

router.beforeEach((to, from) => {
    // instead of having to check every route record with
    // to.matched.some(record => record.meta.requiresAuth)
    if (to.meta.requiresAuth) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!store.state.auth.authenticated) {
            return {
                path: "/login",
                // save the location we were at to come back later
                query: { redirect: to.fullPath },
            };
        }
    } else {
        if (store.state.auth.authenticated) {
            return {
                path: "/",
            };
        }
    }
});

export default router;
