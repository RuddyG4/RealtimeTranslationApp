<template>
    <div class="message me mb-4 flex text-right">
        <div class="flex-1 px-2">
            <div
                class="inline-block bg-blue-600 rounded-full p-2 px-6 text-white"
            >
                <span>{{ message }}</span>
            </div>
            <div class="pr-4">
                <small class="text-gray-500">15 April</small>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    message: {
        type: String,
        required: true,
    },
});
</script>
