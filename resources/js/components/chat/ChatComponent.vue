<template>
    <div v-if="isNotSelectedChat" class="chat-area flex-1">
        <div class="flex flex-col justify-center items-center gap-8 bg-slate-200 rounded-lg h-[98%]">
            <MessageChatIcon class="opacity-50" />
        <span class="opacity-70">Select a chat (or create a new) and start to chat.</span>
        </div>
    </div>
    <div v-else class="chat-area flex-1 flex flex-col">
        <div class="flex gap-2">
            <div class="w-8 h-8 relative">
                <img
                    class="w-8 h-8 rounded-full mx-auto"
                    src="assets/images/profile-image.png"
                    alt="chat-user"
                />
                <span
                    class="absolute w-3 h-3 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"
                ></span>
            </div>
            <h2 class="text-xl py-1 mb-8 border-b-2 border-gray-200">
                <b>{{ chat.title }}</b>
            </h2>
        </div>
        <div class="messages flex-1 overflow-auto flex flex-col-reverse">
            <Message v-for="message in chat.messages" :message="message" />
        </div>
        <div class="flex-2 pt-4 pb-10">
            <div class="write bg-white shadow flex rounded-lg">
                <div
                    class="flex-3 flex content-center items-center text-center p-4 pr-0"
                >
                    <span
                        class="block text-center text-gray-400 hover:text-gray-800"
                    >
                        <EmoticonIcon />
                    </span>
                </div>
                <div class="flex-1">
                    <textarea
                        v-model="message"
                        class="w-full block outline-none py-4 px-4 bg-transparent"
                        rows="1"
                        placeholder="Type a message..."
                        autofocus
                        @keyup.enter="sendMessage"
                    ></textarea>
                </div>
                <div class="flex-2 w-32 p-2 flex content-center items-center">
                    <div class="flex-1 text-center">
                        <span class="text-gray-400 hover:text-gray-800">
                            <span class="inline-block align-text-bottom">
                                <PaperClipIcon />
                            </span>
                        </span>
                    </div>
                    <div class="flex-1">
                        <button
                            type="button"
                            @click="sendMessage"
                            :disabled="isSending || message === ''"
                            class="w-10 h-10 rounded-full inline-block"
                            :class="[
                                message === ''
                                    ? 'cursor-not-allowed bg-blue-400'
                                    : 'bg-blue-600',
                            ]"
                        >
                            <span class="inline-block align-text-bottom">
                                <CheckIcon />
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import EmoticonIcon from "../icons/EmoticonIcon.vue";
import Message from "./MessageComponent.vue";
import PaperClipIcon from "../icons/PaperClipIcon.vue";
import CheckIcon from "../icons/CheckIcon.vue";
import MessageTypes from "@/Enums/MessageTypes";
import { computed, ref } from "vue";
import MessageChatIcon from "../icons/MessageChatIcon.vue";

const emit = defineEmits(["newMessage"]);
const props = defineProps({
    chat: {
        type: Object,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

const isNotSelectedChat = computed(() => {
    return (
        props.chat &&
        Object.keys(props.chat).length === 0 &&
        props.chat.constructor === Object
    );
});

const message = ref("");
const isSending = ref(false);

const sendMessage = async () => {
    if (message.value === "") {
        return;
    }

    isSending.value = true;

    const params = {
        chatId: props.chat.id,
        content: message.value,
        type: MessageTypes.TEXT,
    };
    message.value = "";

    try {
        const response = await axios.post("/api/messages", params);

        const newMessage = response.data.message;
        emit("newMessage", newMessage);
    } catch (error) {
        console.error(error);
    } finally {
        isSending.value = false;
    }
};
</script>
