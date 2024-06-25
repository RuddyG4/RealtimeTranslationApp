<template>
    <div v-if="isLoading" class="h-full px-2">
        <div
            class="flex justify-center items-center bg-gray-200 text-center h-[98%] rounded"
        >
            <span class="text-gray-600">loading...</span>
        </div>
    </div>
    <div v-else-if="props.chats.length === 0" class="h-full px-2">
        <div
            class="flex justify-center items-center bg-gray-200 text-center h-[98%] rounded"
        >
            <span class="text-gray-600">There are no chats yet</span>
        </div>
    </div>
    <div v-else class="flex-1overflow-auto px-2">
        <div
            v-for="chat in props.chats"
            :key="chat.id"
            @click="$emit('chatSelected', chat)"
            class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md"
            :class="{
                'border-l-4 border-red-500': chat.id === props.selectedChatId,
            }"
        >
            <ChatListItem :chat="chat" />
        </div>
    </div>
</template>

<script setup>
import ChatListItem from "./ChatListItem.vue";

const props = defineProps({
    chats: {
        type: Array,
        required: true,
    },
    isLoading: {
        type: Boolean,
        required: true,
    },
    selectedChatId: {
        type: Number,
        default: -1,
    },
});

const emit = defineEmits(["chatSelected"]);
</script>
