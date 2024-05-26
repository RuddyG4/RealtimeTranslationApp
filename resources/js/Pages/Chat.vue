<template>
    <div class="main flex-1 flex flex-col">
        <div class="hidden lg:block heading flex-2">
            <h1 class="text-3xl text-gray-700 mb-4">Chat</h1>
        </div>

        <div class="flex-1 flex h-full">
            <ChatListContainer />
            <Chat />
        </div>
    </div>
</template>

<script setup>
import Chat from "../components/chat/ChatComponent.vue";
import ChatListContainer from "../components/chat/ChatListContainer.vue";
import { ref } from "vue";

const chats = ref([]);
</script>
