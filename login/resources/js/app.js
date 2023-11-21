import { createApp } from "vue";
import LoginForm from "./components/LoginForm.vue";
import RegisterForm from "./components/RegisterForm.vue";

const app = createApp({});

app.component("login-form", LoginForm);
app.component("register-form", RegisterForm);

app.mount("#app");
