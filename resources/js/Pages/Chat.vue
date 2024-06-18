<template>
    <div class="main flex-1 flex flex-col overflow-auto">
        <div class="hidden lg:block heading flex-2">
            <h1 class="text-3xl text-gray-700 mb-4">Chat</h1>
        </div>

        <div class="flex-1 flex h-[90%]">
            <div
                class="sidebar relative lg:flex lg:w-1/3 flex-2 flex-col pr-6"
                :class="{ hidden: !isChatListOpen }"
            >
                <div class="search flex-2 pb-6 px-2">
                    <input
                        v-model="searchText"
                        type="text"
                        class="outline-none py-2 block w-full bg-transparent border-b-2 border-gray-200"
                        placeholder="Search"
                    />
                </div>
                <ChatList
                    :chats="filteredChats"
                    :isLoading="isLoadingChats"
                    :selectedChatId="selectedChat.id"
                    @chatSelected="selectChat"
                />
                <div class="absolute right-14 bottom-8">
                    <button
                        @click="isShowModal = true"
                        type="button"
                        class="rounded-full bg-green-500 px-4 py-2 flex justify-center items-center"
                    >
                        <span class="text-sm text-white mr-2">New chat</span>
                        <PlusIcon color="white" />
                    </button>
                </div>
            </div>
            <Chat
                :chat="selectedChat"
                :userId="store.state.auth.user.id"
                @newMessage="addNewMessage"
            />
        </div>
    </div>

    <NewChatModal
        :isShowModal="isShowModal"
        @closeNewChatModal="isShowModal = false"
        @chatCreated="pushNewChat"
    />
</template>

<script setup>
import { useStore } from "vuex";
import Chat from "../components/chat/ChatComponent.vue";
import ChatList from "../components/chat/ChatList.vue";
import { computed, ref } from "vue";
import PlusIcon from "../components/icons/PlusIcon.vue";
import NewChatModal from "../components/chat/NewChatModal.vue";
import ChatType from "@/Enums/ChatTypes";

const props = defineProps({
    isChatListOpen: {
        type: Boolean,
        default: false,
    },
});
const store = useStore();
const searchText = ref("");
const chats = ref([]);
const selectedChat = ref({});
const isLoadingChats = ref(false);
const isShowModal = ref(false);

const setChatTitle = (chat) => {
    let title = "";
    if (chat.type === ChatType.PRIVATE) {
        const userId = store.state.auth.user.id;
        const userToChat = chat.members.find((member) => member.id !== userId);
        title = userToChat.first_name + " " + userToChat.last_name;
    } else if (chat.type === ChatType.GROUP) {
        title = chat.subject;
    } else {
        // ChatType.PERSONAL
        title =
            store.state.auth.user.first_name +
            " " +
            store.state.auth.user.last_name +
            "(You)";
    }
    return title;
};

const setChatUserState = (chat) => {
    let state = null;
    if (chat.type === ChatType.PRIVATE) {
        const userToChat = chat.members.find(
            (member) => member.id !== store.state.auth.user.id
        );
        state = userToChat.state;
    }
    return state;
};

const computedChats = computed(() => {
    return chats.value.map((chat) => {
        let title = setChatTitle(chat);
        const state =
            chat.state !== null && chat.state !== undefined
                ? chat.state
                : setChatUserState(chat);
        return {
            ...chat,
            title,
            state,
        };
    });
});

const filteredChats = computed(() => {
    const search = searchText.value.toLowerCase();
    if (!search) {
        return computedChats.value;
    }
    return computedChats.value.filter((chat) =>
        chat.title.toLowerCase().includes(search)
    );
});

const getChats = async () => {
    isLoadingChats.value = true;
    try {
        const response = await axios.get(
            `/api/users/${store.state.auth.user.id}/chats`
        );
        chats.value = response.data.chats;
    } catch (error) {
        //TODO: Handle error
        console.error(error);
    } finally {
        isLoadingChats.value = false;
    }
};

getChats();

const pushNewChat = (chat) => {
    chats.value.push(chat);
    selectedChat.value = {
        ...chat,
        title: setChatTitle(chat),
        state: setChatUserState(chat),
    };
};

const selectChat = (chat) => {
    selectedChat.value = chat;
};

const addNewMessage = (message) => {
    const newMessageChat = chats.value.find(
        (chat) => chat.id === message.chat_id
    );
    let i = 0;
    const translatedMessages = message.text_messages;
    while (!message.translated_text && i < translatedMessages.length) {
        if (
            translatedMessages[i].language_id ===
            store.state.auth.user.language_id
        ) {
            message.translated_text = translatedMessages[i];
            message.translated_text = translatedMessages[i];
        }
        i++;
    }
    delete message.original_text;
    delete message.text_messages;
    newMessageChat.messages.unshift(message);
    newMessageChat.latest_message = message;
};

const Channel = Echo.private(`chatUsers.${store.state.auth.user.id}`);
Channel.listen("MessageSent", (e) => {
    const chatId = e.message.chat_id;
    const chat = chats.value.find((chat) => chat.id === chatId);
    if (chat) {
        addNewMessage(e.message);
    }
});

Channel.listen("UserStateChanged", (e) => {
    console.log(e.user);
    const userId = e.user.id;
    const chat = chats.value.find(
        (chat) =>
            chat.type === ChatType.PRIVATE &&
            chat.members.some((member) => member.id === userId)
    );
    if (chat) {
        chat.state = e.user.state;
    }
});
</script>
