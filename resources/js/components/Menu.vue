<template>
    <div
        class="xl:block sm:flex-2 w-64 bg-gray-200"
        :class="isOpen ? 'block' : 'hidden'"
    >
        <div class="user-profile text-center">
            <div
                class="w-32 h-32 rounded-full m-auto mt-16 border-2 border-white bg-white shadow-lg"
            >
                <img
                    src="assets/images/profile-image.png"
                    alt="user"
                    class="block"
                />
            </div>
            <div class="text-gray-800 mt-8 flex justify-center items-center gap-4">
                <div class="flex flex-col">
                    <span>{{ store.state.auth.user.first_name }}</span>
                    <span class="text-gray-500">({{ store.state.auth.user.language.name }})</span>
                </div>
                <span class="inline-block align-text-bottom">
                    <ArrowDownIcon />
                </span>
            </div>
        </div>

        <div class="menu mt-8">
            <router-link
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                :to="{ name: 'home' }"
            >
                <HomeIcon />
                Home
            </router-link>
            <router-link
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                :to="{ name: 'chat' }"
            >
                <span class="inline-block align-text-bottom mr-2">
                    <ChatIcon />
                </span>
                Chat
            </router-link>
            <router-link
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                :to="{ name: 'profile' }"
            >
                <span class="inline-block align-text-bottom mr-2">
                    <CalendarIcon />
                </span>
                Calendar
            </router-link>
            <a
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                href="javascript:return false;"
            >
                <span class="inline-block align-text-bottom mr-2">
                    <FilesIcon />
                </span>
                Files
            </a>
            <a
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                href="javascript:return false;"
            >
                <span class="inline-block align-text-bottom mr-2">
                    <UsersGroupIcon />
                </span>
                Jobs
            </a>
            <a
                class="block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black"
                href="javascript:return false;"
            >
                <span class="inline-block align-text-bottom mr-2">
                    <SettingsIcon />
                </span>
                Settings
            </a>
        </div>
    </div>
</template>

<script setup>
import HomeIcon from "./icons/HomeIcon.vue";
import ChatIcon from "./icons/ChatIcon.vue";
import FilesIcon from "./icons/FilesIcon.vue";
import SettingsIcon from "./icons/SettingsIcon.vue";
import CalendarIcon from "./icons/CalendarIcon.vue";
import UsersGroupIcon from "./icons/UsersGroupIcon.vue";
import ArrowDownIcon from "./icons/ArrowDownIcon.vue";
import { useStore } from "vuex";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
        required: true,
    },
});
const store = useStore();
</script>
