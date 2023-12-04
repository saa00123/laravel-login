<template>
  <div class="max-w-7xl mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <!-- 제목 및 검색 입력란 -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-4xl font-bold text-gray-800">전체 할 일 목록</h1>
      <input
        type="text"
        placeholder="검색..."
        class="border-2 rounded-lg p-2"
        v-model="searchQuery"
      />
    </div>

    <!-- 로그아웃 및 관리자 대시보드 버튼 -->
    <div class="mb-6">
      <button @click="logout" class="border-2 rounded-lg p-1">로그아웃</button>
      <button
        v-if="isAdmin"
        @click="goToAdminDashboard"
        class="border-2 rounded-lg p-1 ml-2"
      >
        관리자 대시보드
      </button>
    </div>

    <!-- Todo 목록 -->
    <ul class="space-y-3">
      <li
        v-for="todo in paginatedTodos"
        :key="todo.id"
        class="border-2 rounded-lg p-3 flex justify-between items-center"
      >
        <div>
          <span class="font-semibold mr-3">작성자: {{ todo.user.name }}</span>
          <span>내용: {{ todo.title }}</span>
        </div>
      </li>
    </ul>

    <!-- 페이지네이션 -->
    <div class="mt-6 flex justify-center">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="currentPage = page"
        class="border p-2 mx-1"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

/** 상태 관리를 위한 참조 변수들 */
const todos = ref([]);
const searchQuery = ref("");
const router = useRouter();
const isAdmin = computed(() => VueCookieNext.getCookie("isAdmin") === "true");
const currentPage = ref(1);
const itemsPerPage = 10;
const pollInterval = 5000;
let poller = null;

/** 모든 사용자의 Todo 목록을 가져오는 함수 */
const fetchTodos = async () => {
  try {
    const response = await axios.get("/api/todos/all");
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

const paginatedTodos = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredTodos.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => {
  return Math.ceil(filteredTodos.value.length / itemsPerPage);
});

/**  폴링을 시작하는 함수 */
const startPolling = () => {
  poller = setInterval(() => {
    fetchTodos();
  }, pollInterval);
};

/** 폴링을 중단하는 함수 */
const stopPolling = () => {
  if (poller) {
    clearInterval(poller);
  }
};

onMounted(() => {
  fetchTodos();
  startPolling();
});

onUnmounted(() => {
  stopPolling();
});
</script>
