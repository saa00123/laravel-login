<template>
  <!-- 사용자 등록 폼 템플릿 -->
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Register</h2>
    <form @submit.prevent="register" class="space-y-4">
      <!-- 이름 입력 필드 -->
      <div>
        <input
          v-model="name"
          type="text"
          placeholder="Name"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 이름 입력 오류 메시지 표시 -->
        <div v-if="errorMessages.name" class="text-red-500">
          {{ errorMessages.name }}
        </div>
      </div>
      <!-- 이메일 입력 필드 -->
      <div>
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 이메일 입력 오류 메시지 표시 -->
        <div v-if="errorMessages.email" class="text-red-500">
          {{ errorMessages.email }}
        </div>
      </div>
      <!-- 비밀번호 입력 필드 -->
      <div>
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 비밀번호 입력 오류 메시지 표시 -->
        <div v-if="errorMessages.password" class="text-red-500">
          {{ errorMessages.password }}
        </div>
      </div>
      <!-- 비밀번호 확인 입력 필드 -->
      <div>
        <input
          v-model="passwordConfirmation"
          type="password"
          placeholder="Confirm Password"
          class="border-2 rounded-lg p-1 w-full"
        />
        <!-- 비밀번호 확인 입력 오류 메시지 표시 -->
        <div v-if="errorMessages.passwordConfirmation" class="text-red-500">
          {{ errorMessages.passwordConfirmation }}
        </div>
      </div>
      <!-- 등록 버튼 -->
      <button type="submit" class="border-2 rounded-lg p-1 w-full">
        Register
      </button>
    </form>
  </div>
</template>

<script setup>
/** 필요한 라이브러리 및 모듈 import */
import axios from "axios";
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";

/** 사용자 입력 데이터 및 오류 메시지 상태 관리 */
const router = useRouter();
const name = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref(""); // 비밀번호 확인 필드
const errorMessages = reactive({
  name: null,
  email: null,
  password: null,
  passwordConfirmation: null, // 비밀번호 확인 오류 메시지
});

/** 사용자 등록 처리 함수 */
const register = async () => {
  try {
    const response = await axios.post("/api/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    // 오류 메시지 초기화 및 라우팅
    errorMessages.name = null;
    errorMessages.email = null;
    errorMessages.password = null;
    errorMessages.passwordConfirmation = null;

    // 등록 성공 메시지 표시 (옵션)
    alert("Registration successful. Please log in.");

    router.push("/");
  } catch (error) {
    // 등록 실패 시 오류 처리
    if (error.response && error.response.data.errors) {
      errorMessages.name = error.response.data.errors.name || null;
      errorMessages.email = error.response.data.errors.email || null;
      errorMessages.password = error.response.data.errors.password || null;
      errorMessages.passwordConfirmation =
        error.response.data.errors.password_confirmation || null;
    } else if (error.response && error.response.data.message) {
      console.error("Registration failed:", error.response.data.message);
    } else {
      console.error("Registration failed:", error);
    }
  }
};
</script>
