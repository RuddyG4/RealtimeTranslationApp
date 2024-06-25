<template>
    <fwb-modal v-if="props.isShowModal" @close="closeModal">
        <template #header>
            <div class="flex items-center text-lg">New Chat</div>
        </template>
        <template #body>
            <fwb-input
                v-model="userSearchQuery"
                label="Search"
                placeholder="enter your search query"
                size="lg"
            >
                <template #prefix>
                    <svg
                        aria-hidden="true"
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                        />
                    </svg>
                </template>
                <template v-if="isQueryingUsers" #suffix>
                    <FwbSpinner size="6">Search</FwbSpinner>
                </template>
            </fwb-input>

            <div v-if="userSearchQuery" class="relative">
                <FwbListGroup class="absolute w-full">
                    <FwbListGroupItem
                        v-for="user in userListQueried"
                        :key="user.id"
                        @click="startNewChat(user)"
                        :disabled="isStartingNewChat"
                        hover
                    >
                        <template #prefix>
                            <div
                                class="flex relative w-5 h-5 bg-orange-500 justify-center items-center m-1 mr-2 mt-1 rounded-full"
                            >
                                <img
                                    class="rounded-full"
                                    alt="A"
                                    :src="user.photo"
                                />
                            </div>
                        </template>
                        <div class="w-full items-center flex">
                            <div class="mx-2 -mt-1">
                                {{ user.first_name + " " + user.last_name }}
                                <small class="text-gray-500">({{ user.language.name }})</small>
                                <div
                                    class="text-xs truncate w-full normal-case font-normal -mt-1 text-gray-500"
                                >
                                    {{ user.email }}
                                </div>
                            </div>
                        </div>
                    </FwbListGroupItem>
                </FwbListGroup>
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <fwb-button @click="closeModal" color="alternative">
                    Close
                </fwb-button>
            </div>
        </template>
    </fwb-modal>
</template>

<script setup>
import {
    FwbModal,
    FwbButton,
    FwbInput,
    FwbListGroup,
    FwbListGroupItem,
    FwbSpinner,
} from "flowbite-vue";
import { useDebouncedRef } from "@/Utils/debouncedRef";
import { watch, ref } from "vue";
import { useStore } from "vuex";

const emit = defineEmits(["closeNewChatModal", "chatCreated"]);
const props = defineProps({
    isShowModal: {
        type: Boolean,
        required: true,
    },
});
const store = useStore();
const userSearchQuery = useDebouncedRef("", 300);
const userListQueried = ref([]);
const isQueryingUsers = ref(false);
const isStartingNewChat = ref(false);

watch(userSearchQuery, async (newQuery, oldQuery) => {
    isQueryingUsers.value = true;
    try {
        const params = {
            userQuery: newQuery,
        };
        const res = await axios.get("/api/users/new-users-to-chat", { params });
        userListQueried.value = res.data.userList;
    } catch (error) {
        console.error(error);
    } finally {
        isQueryingUsers.value = false;
    }
});

const startNewChat = async (user) => {
    isStartingNewChat.value = true;
    try {
        const response = await axios.post(
            `/api/users/${store.state.auth.user.id}/chats`,
            {
                userToChat: user,
            }
        );
        emit("chatCreated", response.data.chat);
    } catch (error) {
        //TODO: Handle error
        console.error(error);
    } finally {
        userSearchQuery.value = "";
        userListQueried.value = [];
        isStartingNewChat.value = false;
        closeModal();
    }
};

const closeModal = () => {
    emit("closeNewChatModal");
};
</script>
