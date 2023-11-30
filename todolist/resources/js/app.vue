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
    const currentPage = ref("login"); // 현재 페이지 상태를 관리하는 반응형 참조

    // 로그인 처리 함수
    const handleLogin = (userData) => {
      store.user = userData; // 스토어에 사용자 데이터 설정
    };

    // 회원가입 후 로그인 페이지로 이동 처리 함수
    const handleRegister = () => {
      currentPage.value = "login"; // 현재 페이지를 로그인 페이지로 변경
    };

    // 페이지 변경 처리 함수
    const handleChangePage = (newPage) => {
      currentPage.value = newPage; // 현재 페이지를 변경
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
