<template>
    <div class="chat-area flex-1 flex flex-col">
        <div class="flex-3">
            <h2 class="text-xl py-1 mb-8 border-b-2 border-gray-200">
                Chatting with <b>Mercedes Yemelyan</b>
            </h2>
        </div>
        <div class="messages flex-1 overflow-auto">
            <ReceivedMessage
                message="Hey there. We would like to invite you over to our office
                    for a visit. How about it?"
            />
            <ReceivedMessage
                message="All travel expenses are covered by us of
                                    course :D"
            />
            <SentMessage message="It's like a dream come true" />
            <SentMessage message="I accept. Thank you very much." />
            <ReceivedMessage
                message="You are welome. We will stay in
                                    touch."
            />
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
                        name="message"
                        class="w-full block outline-none py-4 px-4 bg-transparent"
                        rows="1"
                        placeholder="Type a message..."
                        autofocus
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
                            class="bg-blue-400 w-10 h-10 rounded-full inline-block"
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
import EmoticonIcon from '../icons/EmoticonIcon.vue';
import ReceivedMessage from './ReceivedMessage.vue';
import SentMessage from './SentMessage.vue';
import PaperClipIcon from '../icons/PaperClipIcon.vue';
import CheckIcon from '../icons/CheckIcon.vue';

</script>