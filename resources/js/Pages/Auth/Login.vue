<template>
    <!-- component -->
    <div class="bg-white dark:bg-gray-900">
        <div class="flex justify-center h-screen">
            <div
                class="hidden bg-cover lg:block lg:w-3/5"
                style="
                    background-image: url(https://images.unsplash.com/photo-1616763355603-9755a640a287?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80);
                "
            >
                <div
                    class="flex items-center h-full px-20 bg-gray-900 bg-opacity-40"
                >
                    <div>
                        <h2 class="text-4xl font-bold text-white">Brand</h2>

                        <p class="max-w-xl mt-3 text-gray-300">
                            Lorem ipsum dolor sit, amet consectetur adipisicing
                            elit. In autem ipsa, nulla laboriosam dolores,
                            repellendus perferendis libero suscipit nam
                            temporibus molestiae
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/5"
            >
                <div class="flex-1">
                    <div class="text-center">
                        <h2
                            class="text-4xl font-bold text-center text-gray-700 dark:text-white"
                        >
                            Brand
                        </h2>

                        <p class="mt-3 text-gray-500 dark:text-gray-300">
                            Sign in to access your account
                        </p>
                        <div
                            v-if="errorMessage"
                            class="font-regular relative block w-full rounded-lg bg-red-500 p-4 text-base leading-5 text-white opacity-100 mt-4"
                            data-dismissible="alert"
                        >
                            <div class="mr-12">{{ errorMessage }}</div>
                            <div
                                class="absolute top-2.5 right-3 w-max rounded-lg transition-all hover:bg-white hover:bg-opacity-20"
                                data-dismissible-target="alert"
                            >
                                <button
                                    type="button"
                                    @click="errorMessage = null"
                                    role="button"
                                    class="w-max rounded-lg p-1"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <form @submit.prevent="login">
                            <div>
                                <label
                                    for="email"
                                    class="block mb-2 text-sm text-gray-600 dark:text-gray-200"
                                    >Email Address</label
                                >
                                <input
                                    type="email"
                                    v-model="user.email"
                                    id="email"
                                    placeholder="example@example.com"
                                    class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-md dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400 dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                                    tabindex="1"
                                />
                                <span
                                    v-if="validationErrors.email"
                                    class="text-red-400 text-sm mt-1"
                                >
                                    {{ validationErrors.email[0] }}
                                </span>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between mb-2">
                                    <label
                                        for="password"
                                        class="text-sm text-gray-600 dark:text-gray-200"
                                    >
                                        Password
                                    </label>
                                    <a
                                        href="#"
                                        class="text-sm text-gray-400 focus:text-blue-500 hover:text-blue-500 hover:underline"
                                    >
                                        Forgot password?
                                    </a>
                                </div>

                                <input
                                    type="password"
                                    v-model="user.password"
                                    id="password"
                                    placeholder="Your Password"
                                    class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-md dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400 dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                                    tabindex="2"
                                />
                                <span
                                    v-if="validationErrors.password"
                                    class="text-red-400 text-sm mt-1"
                                >
                                    {{ validationErrors.password[0] }}
                                </span>
                            </div>

                            <div class="mt-6">
                                <button
                                    :disabled="processing"
                                    class="w-full px-4 py-2 flex justify-center tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded-md hover:bg-blue-400 focus:outline-none focus:bg-blue-400 focus:ring focus:ring-blue-300 focus:ring-opacity-50"
                                >
                                    <FwbSpinner size="6" v-if="processing" />
                                    <span v-else>Sign in</span>
                                </button>
                            </div>
                        </form>

                        <p class="mt-6 text-sm text-center text-gray-400">
                            Don&#x27;t have an account yet?
                            <RouterLink
                                :to="{ name: 'register' }"
                                class="text-blue-500 focus:outline-none focus:underline hover:underline"
                            >
                                Sign up
                            </RouterLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { FwbSpinner } from "flowbite-vue";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const router = useRouter();
const store = useStore();
const user = ref({
    email: "",
    password: "",
});
const validationErrors = ref({});
const errorMessage = ref("");
const processing = ref(false);

const login = async () => {
    processing.value = true;
    axios
        .post("/login", user.value)
        .then((response) => {
            store.commit("auth/setUser", response.data.user);
            store.commit("auth/setAuthenticated", true);
            router.push({ name: "home" });
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                validationErrors.value = error.response.data.errors;
            } else {
                errorMessage.value = error.response.data.message;
            }
        })
        .finally(() => {
            processing.value = false;
        });
};
</script>
