<template>
    <div class="w-full h-screen">
        <div class="flex h-full">
            <Menu :isOpen="isOpen" />
            <div class="flex-1 bg-gray-100 w-full h-full">
                <div
                    class="main-body container m-auto w-11/12 h-full flex flex-col"
                >
                    <div class="py-4 flex-2 flex flex-row">
                        <div class="flex-1">
                            <span
                                class="xl:hidden inline-block text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400"
                                    @click="isOpen = !isOpen"
                                >
                                    <MenuIcon v-if="!isOpen" />
                                    <CloseIcon v-else />
                                </span>
                            </span>
                            <span
                                class="lg:hidden inline-block ml-8 text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        ></path>
                                    </svg>
                                </span>
                            </span>
                        </div>
                        <div class="flex-1 text-right">
                            <span class="inline-block text-gray-700">
                                Status:
                                <span
                                    class="inline-block align-text-bottom w-4 h-4 bg-green-400 rounded-full border-2 border-white"
                                ></span>
                                <b class="hidden md:inline-block">Online</b>
                                <span class="inline-block align-text-bottom">
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                        class="w-4 h-4"
                                    >
                                        <path d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </span>

                            <span
                                class="inline-block ml-8 text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400 cursor-pointer"
                                >
                                    <NotificationIcon />
                                </span>
                            </span>
                            <span
                                class="inline-block ml-8 text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    @click="logout"
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400 cursor-pointer"
                                >
                                    <RightFromBracketIcon />
                                </span>
                            </span>
                        </div>
                    </div>

                    <router-view />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import Menu from "./Menu.vue";
import MenuIcon from "../components/icons/MenuIcon.vue";
import CloseIcon from "../components/icons/CloseIcon.vue";
import NotificationIcon from "./icons/NotificationIcon.vue";
import RightFromBracketIcon from "./icons/RightFromBracketIcon.vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const isOpen = ref(false);
const processing = ref(false);
const errorMessage = ref("");
const router = useRouter();
const store = useStore();

const logout = async () => {
    processing.value = true;
    axios
        .post("/logout")
        .then((response) => {
            store.commit("auth/setUser", {});
            store.commit("auth/setAuthenticated", false);
            router.push({ name: "login" });
        })
        .catch((error) => {
            errorMessage.value = error.response.data.message;
        })
        .finally(() => {
            processing.value = false;
        });
};
</script>
