import { createApp } from "vue";
import TodoList from "./components/TodoList.vue";

const app = createApp({
    components: {
        "todo-list": TodoList,
    },
});

app.mount("#app");
