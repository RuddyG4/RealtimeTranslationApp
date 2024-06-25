<template>
    <div
        class="message mb-4 flex"
        :class="{ 'me text-right': type === 'sent' }"
    >
        <div class="flex-1 px-2">
            <div
                class="inline-block p-2 px-6"
                :class="{
                    'bg-blue-600 text-white rounded-tr-full rounded-bl-full rounded-tl-full':
                        type === 'sent',
                    'bg-gray-300 text-gray-700 rounded-tr-full rounded-br-full rounded-tl-full':
                        type === 'received',
                }"
            >
                <span v-if="message.type === MessageTypes.TEXT">
                    {{ text }}
                </span>
                <audio
                    v-else-if="message.type === MessageTypes.AUDIO"
                    :src="message.translated_audio?.path"
                    controls
                >
                    Audios not supported
                </audio>
            </div>
            <!-- <div :class="[type === 'sent' ? 'pr-4' : 'pl-4']">
                <small class="text-gray-500">
                    {{ time }}
                </small>
            </div> -->
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { format } from "@formkit/tempo";
import MessageTypes from "@/Enums/MessageTypes";
import { useStore } from "vuex";

const store = useStore();
const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
});

const time = computed(() => {
    return format(
        props.message.sent_at,
        { time: "short" },
        store.state.auth.user.language.code
    );
});

const text = computed(() => {
    return props.message.translated_text
        ? props.message.translated_text.content
        : "No translated message";
});

const type = computed(() => {
    return props.message.user_id === store.state.auth.user.id
        ? "sent"
        : "received";
});
</script>
