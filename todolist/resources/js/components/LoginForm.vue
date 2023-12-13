<template>
  <!-- 로그인 폼 템플릿 -->
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Login</h2>
    <form @submit.prevent="login" class="flex-col space-y-4">
      <!-- 이메일 입력 필드 -->
      <div class="flex-col">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 이메일 오류 메시지 표시 -->
        <div v-if="errorMessages.email" class="text-red-500">
          {{ errorMessages.email }}
        </div>
      </div>
      <!-- 비밀번호 입력 필드 -->
      <div class="flex-col">
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 비밀번호 오류 메시지 표시 -->
        <div v-if="errorMessages.password" class="text-red-500">
          {{ errorMessages.password }}
        </div>
      </div>
      <!-- 로그인 버튼 -->
      <button type="submit" class="border-2 rounded-lg p-1 w-full">
        Login
      </button>
    </form>
    <!-- 회원가입 페이지로 이동하는 버튼 -->
    <button @click="goToRegister" class="border-2 rounded-lg p-1 w-full mt-4">
      Register
    </button>
  </div>
</template>

<script setup>
/** 필요한 라이브러리 및 모듈 import */
import axios from "axios";
import { reactive, toRefs } from "vue";
import { useRouter } from "vue-router";
import { store } from "../store";
import { VueCookieNext } from "vue-cookie-next";

/** 로그인 폼 데이터와 오류 메시지 상태 관리 */
const loginForm = reactive({
  email: "",
  password: "",
  errorMessages: {
    email: null,
    password: null,
  },
});

const { email, password, errorMessages } = toRefs(loginForm);
const router = useRouter();

/** 로그인 처리 함수 */
const login = async () => {
  try {
    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
    });

    // 오류 메시지 초기화
    errorMessages.value.email = null;
    errorMessages.value.password = null;

    // 토큰 및 사용자 데이터 저장 및 라우팅 처리
    VueCookieNext.setCookie("token", response.data.access_token, {
      expires: "1d",
    });
    // axios.defaults.headers.common[
    //   "Authorization"
    // ] = `Bearer ${response.data.access_token}`;

    const user = response.data.user;
    VueCookieNext.setCookie("isAdmin", user.is_admin ? "true" : "false", {
      expires: "1d",
    });
    if (response.data.user.is_admin) {
      router.push("/admin");
    } else {
      const userId = response.data.user.id;
      router.push(`/${userId}/todos`);
    }

    if (user.is_admin) {
      router.push("/admin");
    } else {
      router.push(`/${user.id}/todos`);
    }

    store.user = { ...response.data.user, registered: true };
  } catch (error) {
    // 로그인 실패 시 오류 처리
    if (error.response && error.response.data.errors) {
      errorMessages.value.email = error.response.data.errors.email || null;
      errorMessages.value.password =
        error.response.data.errors.password || null;
    } else {
      console.error("Login failed:", error);
    }
  }
};

/** 회원가입 페이지로 이동하는 함수 */
const goToRegister = () => {
  router.push("/register");
};
</script>
