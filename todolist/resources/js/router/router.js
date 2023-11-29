import { createRouter, createWebHistory } from "vue-router";
import LoginForm from "../components/LoginForm.vue";
import RegisterForm from "../components/RegisterForm.vue";
import TodoList from "../components/TodoList.vue";
import AdminPage from "../components/AdminPage.vue";
import { store } from "../store";
import { VueCookieNext } from "vue-cookie-next";

const routes = [
  { path: "/", component: LoginForm },
  { path: "/register", component: RegisterForm },
  { path: "/:userId/todos", component: TodoList, props: true },
  { path: "/admin", component: AdminPage, meta: { requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAdmin = VueCookieNext.getCookie("isAdmin") === "true";

  if (to.matched.some((record) => record.meta.requiresAdmin) && !isAdmin) {
    next({ path: "/" });
  } else {
    next();
  }
});

export default router;
