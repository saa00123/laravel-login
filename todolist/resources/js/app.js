import { createApp } from "vue";
import App from "./App.vue";
import router from "./router/router";
import axios from "axios";

axios.defaults.withCredentials = true;
const token = localStorage.getItem("token");

if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

createApp(App).use(router).mount("#app");
