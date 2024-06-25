<template>
    <div class="flex-2">
        <div class="w-12 h-12 relative">
            <img
                class="w-12 h-12 rounded-full mx-auto"
                :src="props.chat.icon"
                alt="chat-user"
            />
            <span
                v-if="props.chat.state !== null"
                class="absolute w-4 h-4 rounded-full right-0 bottom-0 border-2 border-white"
                :class="[UserStates.colors[props.chat.state]]"
            ></span>
        </div>
    </div>
    <div class="flex-1 px-2">
        <div class="truncate w-32">
            <span class="text-gray-800">{{ props.chat.title }}</span>
        </div>
        <div>
            <small v-if="props.chat.latest_message" class="text-gray-600">
                <span v-if="props.chat.latest_message.type === MessageTypes.TEXT">
                    {{ props.chat.latest_message.translated_text.content }}
                </span>
                <span
                    v-else-if="props.chat.latest_message.type === MessageTypes.AUDIO"
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
            <small v-if="props.chat.latest_message" class="text-gray-500">
                {{ format(props.chat.latest_message.sent_at) }}
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
</template>

<script setup>
import { format } from "@formkit/tempo";
import UserStates from "@/Enums/UserStates";
import MessageTypes from "@/Enums/MessageTypes";
import MicrophoneIcon from "../icons/MicrophoneIcon.vue";

const props = defineProps({
    chat: {
        type: Object,
        required: true,
    },
});
</script>
