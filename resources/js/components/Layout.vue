<template>
    <div v-if="store.state.auth.authenticated" class="w-full h-screen">
        <div class="flex h-full">
            <Menu :isOpen="isMenuOpen" />
            <div class="flex-1 bg-gray-100 w-full h-full">
                <div
                    class="main-body container m-auto w-11/12 h-full flex flex-col"
                >
                    <div class="py-4 flex flex-row">
                        <div class="flex-1">
                            <span
                                class="xl:hidden inline-block text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400 cursor-pointer"
                                    @click="isMenuOpen = !isMenuOpen"
                                >
                                    <MenuIcon v-if="!isMenuOpen" />
                                    <CloseIcon v-else />
                                </span>
                            </span>
                            <span
                                class="lg:hidden inline-block ml-8 text-gray-700 hover:text-gray-900 align-bottom"
                            >
                                <span
                                    @click="isChatListOpen = !isChatListOpen"
                                    class="block h-6 w-6 p-1 rounded-full hover:bg-gray-400 cursor-pointer"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        stroke="currentColor"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                        data-icon="SvgMessageSquare"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M21 15a2 2 0 01-2 2H7l-2 2-2 2V5a2 2 0 012-2h14a2 2 0 012 2v10z"
                                        ></path>
                                    </svg>
                                </span>
                            </span>
                        </div>
                        <div class="flex-1 text-right">
                            <span class="inline-block text-gray-700">
                                Status:
                                <span
                                    class="inline-block align-text-bottom w-4 h-4 rounded-full border-2 border-white"
                                    :class="[userState.colorClass]"
                                ></span>
                                <b class="hidden md:inline-block">{{
                                    userState.name
                                }}</b>
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

                    <template v-if="route.name === 'chat'">
                        <router-view :isChatListOpen="isChatListOpen" />
                    </template>
                    <template v-else>
                        <router-view />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import Menu from "./Menu.vue";
import MenuIcon from "../components/icons/MenuIcon.vue";
import CloseIcon from "../components/icons/CloseIcon.vue";
import NotificationIcon from "./icons/NotificationIcon.vue";
import RightFromBracketIcon from "./icons/RightFromBracketIcon.vue";
import { useRoute, useRouter } from "vue-router";
import { useStore } from "vuex";
import UserStates from "@/Enums/UserStates";

const isMenuOpen = ref(false);
const isChatListOpen = ref(false);
const processing = ref(false);
const errorMessage = ref("");
const router = useRouter();
const route = useRoute();
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

const userState = computed(() => {
    return {
        code: store.state.auth.user.state,
        name: UserStates.properties[store.state.auth.user.state],
        colorClass: UserStates.colors[store.state.auth.user.state],
    };
});
</script>

<style>
/* width */
::-webkit-scrollbar {
    width: 8px;
    border-radius: 10px;
}

/* Track */
::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px #a1a1a1;
    border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: rgb(118, 169, 250);
    border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: rgb(28, 100, 242);
}
</style>
