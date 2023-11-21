import { createRouter, createWebHistory } from "vue-router";
import LoginForm from "../components/LoginForm.vue";
import RegisterForm from "../components/RegisterForm.vue";
import TodoList from "../components/TodoList.vue";

const routes = [
  { path: "/", component: LoginForm },
  { path: "/register", component: RegisterForm },
  { path: "/:userId/todos", component: TodoList, props: true },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
