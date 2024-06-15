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
                <span>
                    {{ message }}
                </span>
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

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
        // validator(value) {
        //     return ["sent", "received"].includes(value);
        // },
    },
    date: {
        type: String,
        required: true,
    },
});

const time = computed(() => {
    return format(props.date, { time: "short" }, 'es');
});
</script>
