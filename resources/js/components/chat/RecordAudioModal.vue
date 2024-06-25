<template>
    <FwbModal v-if="isShowModal" @close="closeModal">
        <template #body>
            <div class="flex flex-col gap-4 items-center justify-center">
                <div v-if="audio" class="flex items-center gap-2">
                    <audio :src="audio.src" :controls="audio.controls"></audio>

                    <button
                        @click="sendAudio"
                        type="button"
                        class="px-2 h-8 rounded bg-sky-500"
                    >
                        <SendIcon class="w-4 h-4 text-white" />
                    </button>

                    <button
                        @click="discardRecording" type="button" class="px-2 h-8 rounded bg-red-500">
                        <TrashIcon class="w-4 h-4 text-white" />
                    </button>
                </div>

                <template v-else>
                    <button
                        @click="toggleRecording"
                        type="button"
                        class="relative"
                    >
                        <div
                            :class="[
                                isRecording
                                    ? 'recording-pulse-ring'
                                    : 'pulse-ring',
                            ]"
                        ></div>
                        <div
                            class="flex items-center z-50 w-24 h-24 text-3xl text-white rounded-full"
                            :class="[isRecording ? 'bg-red-500' : 'bg-sky-500']"
                        >
                            <MicrophoneIcon class="w-14 mx-auto" />
                        </div>
                    </button>

                    <span class="opacity-70">{{ text }}</span>
                </template>
            </div>
        </template>
    </FwbModal>
</template>

<script setup>
import { FwbModal } from "flowbite-vue";
import { ref, computed, onMounted } from "vue";
import MicrophoneIcon from "@/components/icons/MicrophoneIcon.vue";
import TrashIcon from "@/components/icons/TrashIcon.vue";
import SendIcon from "@/components/icons/SendIcon.vue";
import RefreshIcon from "../icons/RefreshIcon.vue";

const emit = defineEmits(["showModal", "sendAudio"]);

const props = defineProps({
    isShowModal: {
        type: Boolean,
        required: true,
    },
});

const isRecording = ref(false);
const mediaRecorder = ref(null);
const audioChunks = ref([]);
const audio = ref(null);
const blob = ref(null);
const text = computed(() => {
    return isRecording.value
        ? "Recording... press to stop"
        : "Press to start recording";
});

const toggleRecording = () => {
    if (isRecording.value) {
        stopRecording();
    } else {
        startRecording();
    }
};

const startRecording = () => {
    if (mediaRecorder.value) {
        mediaRecorder.value.start();
        isRecording.value = true;
    }
};

const stopRecording = () => {
    if (mediaRecorder.value) {
        mediaRecorder.value.stop();
        isRecording.value = false;
    }
};

const setupMediaRecorder = async () => {
    if (navigator.mediaDevices.getUserMedia) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: true,
            });
            mediaRecorder.value = new MediaRecorder(stream);

            mediaRecorder.value.ondataavailable = (e) => {
                audioChunks.value.push(e.data);
            };

            mediaRecorder.value.onstop = () => {
                blob.value = new Blob(audioChunks.value, {
                    type: mediaRecorder.value.mimeType,
                });
                audioChunks.value = [];
                const audioURL = URL.createObjectURL(blob.value);
                audio.value = new Audio(audioURL);
                audio.value.controls = true;
                //
            };
        } catch (err) {
            console.error("The following error occurred: " + err);
        }
    } else {
        console.log(
            "MediaDevices.getUserMedia() not supported on your browser!"
        );
    }
};

const sendAudio = async () => {
    if (audio.value && blob.value) {
        const formData = new FormData();
        const audioFile = new File([blob.value], "audio.wav", {
            type: blob.value.type,
        });
        formData.append("content", audioFile);
        emit("sendAudio", formData);
        closeModal();
    }
};

onMounted(() => {
    setupMediaRecorder();
});

const discardRecording = () => {
    audio.value = null;
    blob.value = null;
    audioChunks.value = [];
}

const closeModal = () => {
    emit('showModal', false);
    discardRecording();
}
</script>

<style scoped>
@keyframes pulsate {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    20% {
        transform: scale(1.3);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 0;
    }
}

@keyframes recording-pulsate {
    0% {
        transform: scale(1, 1);
        opacity: 1;
    }
    100% {
        transform: scale(1.3, 1.3);
        opacity: 0;
    }
}

.pulse-ring {
    content: "";
    width: 100px;
    height: 100px;
    background: #189bff;
    border: 5px solid #189bff;
    border-radius: 50%;
    position: absolute;
    top: -2px;
    left: -2px;
    animation: pulsate 6.5s infinite;
}

.recording-pulse-ring {
    content: "";
    width: 100px;
    height: 100px;
    background: #189bff;
    border: 5px solid #189bff;
    border-radius: 50%;
    position: absolute;
    top: -2px;
    left: -2px;
    animation: recording-pulsate 2s infinite;
}
</style>
