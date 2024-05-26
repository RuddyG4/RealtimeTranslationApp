<template>
    <div class="message mb-4 flex">
        <div class="flex-2">
            <div class="w-12 h-12 relative">
                <img
                    class="w-12 h-12 rounded-full mx-auto"
                    src="assets/images/profile-image.png"
                    alt="chat-user"
                />
                <span
                    class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"
                ></span>
            </div>
        </div>
        <div class="flex-1 px-2">
            <div
                class="inline-block bg-gray-300 rounded-full p-2 px-6 text-gray-700"
            >
                <span>
                    {{ message }}
                </span>
            </div>
            <div class="pl-4">
                <small class="text-gray-500">15 April</small>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        required: true
    }
})
</script>