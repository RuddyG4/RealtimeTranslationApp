<template>
    <div class="flex flex-col gap-6">
        <h3 class="text-lg font-bold">User profile</h3>

        <div class="flex flex-col gap-3">
            <div class="relative user-profile">
                <div
                    class="w-32 h-32 rounded-full border-2 border-white bg-white shadow-lg relative overflow-hidden"
                >
                    <img
                        :src="store.state.auth.user.photo"
                        alt="user"
                        class="block w-full h-full rounded-full"
                    />
                    <button
                        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white opacity-0 hover:opacity-100 transition-opacity"
                        @click="changeProfilePicture"
                    >
                        Cambiar foto
                    </button>
                </div>
            </div>

            <FwbInput
                v-model="user.first_name"
                label="First Name"
                placeholder="Enter your first name"
                size="sm"
            />

            <FwbInput
                v-model="user.last_name"
                label="Last Name"
                placeholder="Enter your last name"
                size="sm"
            />

            <FwbInput
                v-model="user.email"
                label="Email"
                type="email"
                disabled
                size="sm"
            />
            
            <FwbInput
                v-model="user.language_name"
                label="Language"
                disabled
                size="sm"
            />
        </div>

        <div class="text-right">
            <FwbButton @click="router.back()" color="alternative"
                >Back</FwbButton
            >
            <FwbButton @click="saveChanges" class="ml-4" :disabled="!isChanged"
                >Save changes</FwbButton
            >
        </div>
    </div>

    <!-- loader -->
    <div
        v-if="isLoading"
        class="w-full h-full fixed top-0 left-0 bg-white opacity-75 z-50"
    >
        <div class="flex justify-center items-center mt-[50vh]">
            <FwbSpinner size="8" />
        </div>
    </div>
</template>

<script setup>
import { FwbInput, FwbSpinner, FwbButton } from "flowbite-vue";
import { computed, ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const user = ref({
    first_name: "",
    last_name: "",
    email: "",
    language_name: "",
});
const originalUserData = ref(null);
const isLoading = ref(false);
const store = useStore();
const isChanged = computed(() => {
    return (
        JSON.stringify(originalUserData.value) !== JSON.stringify(user.value)
    );
});

const loadUserData = () => {
    isLoading.value = true;
    axios
        .get(`/api/users/${store.state.auth.user.id}`)
        .then((response) => {
            originalUserData.value = { ...response.data.user };
            user.value = response.data.user;
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};

loadUserData();

const router = useRouter();
const saveChanges = () => {
    isLoading.value = true;
    axios
        .put(`/api/users/${store.state.auth.user.id}`, user.value)
        .then((response) => {
            originalUserData.value = { ...response.data.user };
            user.value = response.data.user;
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};
</script>
