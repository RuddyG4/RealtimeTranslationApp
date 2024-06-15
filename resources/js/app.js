import "./bootstrap";
import router from "./router";
import { createApp } from "vue";
import App from "./App.vue";
import store from "@/store";
import '/node_modules/flowbite-vue/dist/index.css';

const app = createApp(App);

app.use(router).use(store).mount("#app");
