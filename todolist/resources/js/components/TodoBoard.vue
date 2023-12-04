<template>
  <div class="max-w-7xl mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-4xl font-bold text-gray-800">전체 할 일 목록</h1>
      <input
        type="text"
        placeholder="검색..."
        class="border-2 rounded-lg p-2"
        v-model="searchQuery"
      />
    </div>
    <button @click="logout" class="border-2 rounded-lg p-1 mb-6">
      로그아웃
    </button>
    <button
      v-if="isAdmin"
      @click="goToAdminDashboard"
      class="border-2 rounded-lg p-1 mb-6"
    >
      관리자 대시보드
    </button>
    <ul class="space-y-3">
      <li
        v-for="todo in filteredTodos"
        :key="todo.id"
        class="border-2 rounded-lg p-3 flex justify-between items-center"
      >
        <!-- Todo 정보 -->
        <div>
          <span class="font-semibold mr-3">작성자: {{ todo.user.name }}</span>
          <span>내용: {{ todo.title }}</span>
        </div>

        <!-- 수정 및 삭제 버튼 -->
        <div class="flex space-x-2">
          <button
            v-if="isAdmin"
            @click="editTodo(todo)"
            class="border-2 rounded-lg p-1"
          >
            수정
          </button>
          <button
            v-if="isAdmin"
            @click="deleteTodo(todo)"
            class="border-2 rounded-lg p-1"
          >
            삭제
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

/** 상태 관리를 위한 참조 변수들 */
const todos = ref([]);
const searchQuery = ref("");
const router = useRouter();
const isAdmin = computed(() => VueCookieNext.getCookie("isAdmin") === "true");

/** 모든 사용자의 Todo 목록을 가져오는 함수 */
const fetchTodos = async () => {
  try {
    const response = await axios.get("/api/todos/all"); // API 경로 변경
    todos.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

/** 로그아웃 처리 함수 */
const logout = async () => {
  // 로그아웃 로직
};

/** 관리자 대시보드로 이동하는 함수 */
const goToAdminDashboard = () => {
  router.push("/admin");
};

/** 필터링된 Todo 목록을 계산하는 computed 속성 */
const filteredTodos = computed(() => {
  return todos.value.filter(
    (todo) =>
      todo.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      todo.user.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
  );
});

onMounted(fetchTodos);
</script>
