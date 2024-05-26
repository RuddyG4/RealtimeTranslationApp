<template>
    <RouterView v-slot="{ Component, route }">
        <Component :is="Component" :key="route.name" />
    </RouterView>
</template>
