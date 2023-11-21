<template>
  <div>
    <router-view />
  </div>
</template>

<script>
import { ref } from "vue";
import { store } from "./store";
import LoginForm from "./components/LoginForm.vue";
import RegisterForm from "./components/RegisterForm.vue";
import TodoList from "./components/TodoList.vue";

export default {
  components: {
    LoginForm,
    RegisterForm,
    TodoList,
  },
  setup() {
    const currentPage = ref("login");

    const handleLogin = (userData) => {
      store.user = userData;
    };

    const handleRegister = () => {
      currentPage.value = "login";
    };

    const handleChangePage = (newPage) => {
      currentPage.value = newPage;
    };

    return {
      currentPage,
      store,
      handleLogin,
      handleRegister,
      handleChangePage,
    };
  },
};
</script>
