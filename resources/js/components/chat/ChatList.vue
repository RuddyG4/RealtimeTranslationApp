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
            <div class="flex-2">
                <div class="w-12 h-12 relative">
                    <img
                        class="w-12 h-12 rounded-full mx-auto"
                        :src="chat.icon"
                        alt="chat-user"
                    />
                    <span
                        v-if="chat.state !== null"
                        class="absolute w-4 h-4 rounded-full right-0 bottom-0 border-2 border-white"
                        :class="[UserStates.colors[chat.state]]"
                    ></span>
                </div>
            </div>
            <div class="flex-1 px-2">
                <div class="truncate w-32">
                    <span class="text-gray-800">{{ chat.title }}</span>
                </div>
                <div>
                    <small v-if="chat.latest_message" class="text-gray-600">
                        <span
                            v-if="
                                chat.latest_message.type === MessageTypes.TEXT
                            "
                        >
                            {{ chat.latest_message.translated_text.content }}
                        </span>
                        <span
                            v-else-if="
                                chat.latest_message.type === MessageTypes.AUDIO
                            "
                            class="flex"
                        >
                            <MicrophoneIcon class="w-4" />
                            audio message
                        </span>
                    </small>
                    <small v-else class="text-gray-600">No messages</small>
                </div>
            </div>
            <div class="flex-2 text-right">
                <div>
                    <small v-if="chat.latest_message" class="text-gray-500">
                        {{ format(chat.latest_message.sent_at) }}
                    </small>
                </div>
                <div>
                    <small
                        class="text-xs bg-red-500 text-white rounded-full h-6 w-6 leading-6 text-center inline-block"
                    >
                        23
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { format } from "@formkit/tempo";
import UserStates from "@/Enums/UserStates";
import MessageTypes from "@/Enums/MessageTypes";
import MicrophoneIcon from "../icons/MicrophoneIcon.vue";

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
