<template>
    <fwb-modal v-if="props.isShowModal" @close="$emit('closeNewChatModal')">
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
                        @click="addUserToSelectedUsers(user)"
                        hover
                    >
                        <template #prefix>
                            <div
                                class="flex relative w-5 h-5 bg-orange-500 justify-center items-center m-1 mr-2 mt-1 rounded-full"
                            >
                                <img
                                    class="rounded-full"
                                    alt="A"
                                    src="https://randomuser.me/api/portraits/men/62.jpg"
                                />
                            </div>
                        </template>
                        <div class="w-full items-center flex">
                            <div class="mx-2 -mt-1">
                                {{ user.first_name + " " + user.last_name }}
                                <div
                                    class="text-xs truncate w-full normal-case font-normal -mt-1 text-gray-500"
                                >
                                    {{ user.language.name }}
                                </div>
                            </div>
                        </div>
                    </FwbListGroupItem>
                </FwbListGroup>
            </div>

            <div v-if="selectedUsers.length > 0" class="flex mt-4">
                <FwbBadge v-for="user in selectedUsers" size="sm">
                    <template #icon>
                        <button type="button" class="mr-2">
                            <XMarkIcon />
                        </button>
                    </template>
                    {{ user.first_name + " " + user.last_name }}
                </FwbBadge>
            </div>

            <div class="mt-4">
                <FwbTextarea
                    v-model="message"
                    :rows="4"
                    label="Your message"
                    placeholder="Write your message..."
                />
            </div>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <fwb-button
                    @click="$emit('closeNewChatModal')"
                    color="alternative"
                >
                    Close
                </fwb-button>
                <fwb-button
                    class="ml-2"
                    @click="$emit('closeNewChatModal')"
                    color="green"
                >
                    I accept
                </fwb-button>
            </div>
        </template>
    </fwb-modal>
</template>

<script setup>
import XMarkIcon from "../icons/XMarkIcon.vue";
import {
    FwbModal,
    FwbButton,
    FwbInput,
    FwbListGroup,
    FwbListGroupItem,
    FwbSpinner,
    FwbBadge,
    FwbTextarea,
} from "flowbite-vue";
import { useDebouncedRef } from "@/Utils/debouncedRef";
import { computed, watch, ref } from "vue";

defineEmits(["closeNewChatModal"]);
const props = defineProps({
    isShowModal: {
        type: Boolean,
        required: true,
    },
});
const userSearchQuery = useDebouncedRef("", 300);
const userListQueried = ref([]);
const selectedUsers = ref([]);
const isQueryingUsers = ref(false);
const message = ref("");

watch(userSearchQuery, async (newQuery, oldQuery) => {
    isQueryingUsers.value = true;
    try {
        const params = {
            userQuery: newQuery,
        };
        const res = await axios.get("/api/users", { params });
        userListQueried.value = res.data.userList;
    } catch (error) {
        console.error(error);
    } finally {
        isQueryingUsers.value = false;
    }
});

const addUserToSelectedUsers = (user) => {
    selectedUsers.value.push(user);
    userSearchQuery.value = "";
    userListQueried.value = [];
};
</script>
