<template>
  <!-- 할 일 목록 페이지 템플릿 -->
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">할 일 목록</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      로그아웃
    </button>
    <!-- 관리자 대시보드로 이동하는 버튼 -->
    <button
      v-if="isAdmin"
      @click="goToAdminDashboard"
      class="border-2 rounded-lg p-1 w-full mb-6"
    >
      관리자 대시보드로 이동
    </button>
    <!-- 할 일 추가 입력 필드 -->
    <div v-if="userPermissions.create_allowed" class="flex items-center mb-4">
      <input
        type="text"
        class="border-2 rounded-lg p-1 w-full mr-2"
        v-model="newTodo"
        @keyup.enter="addTodo"
        placeholder="새 할 일 추가..."
      />
      <button class="border-2 rounded-lg p-1" @click="addTodo">추가</button>
    </div>
    <!-- 할 일 추가 오류 메시지 표시 -->
    <div v-if="errorMessages.addTodo" class="text-red-500">
      {{ errorMessages.addTodo }}
    </div>
    <!-- 할 일 목록 표시 -->
    <ul class="space-y-3">
      <li
        v-for="todo in todos"
        :key="todo.id"
        :class="{ 'line-through text-gray-500': todo.completed }"
        class="flex border-2 rounded-lg items-center justify-between p-3"
      >
        <div class="flex items-center">
          <!-- 할 일 완료 여부 체크박스 -->
          <input
            type="checkbox"
            v-model="todo.completed"
            @change="updateTodo(todo)"
            class="mr-3"
          />
          <!-- 할 일 제목 표시 및 수정 입력 필드 -->
          <span v-if="!todo.editing">{{ todo.title }}</span>
          <input
            v-if="todo.editing"
            type="text"
            v-model="todo.updatedTitle"
            @blur="saveUpdatedTodo(todo)"
            @keyup.enter="saveUpdatedTodo(todo)"
            class="border-2 rounded-lg p-1"
          />
        </div>
        <!-- 할 일 수정 및 삭제 버튼 -->
        <div class="flex space-x-2">
          <button
            v-if="userPermissions.update_allowed"
            @click="editTodo(todo)"
            class="border-2 rounded-lg p-1"
          >
            수정
          </button>
          <button
            v-if="userPermissions.delete_allowed"
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
/** 필요한 라이브러리 및 모듈 import */
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

/** 상태 관리를 위한 참조 변수들 */
const todos = ref([]);
const newTodo = ref("");
const router = useRouter();
const userPermissions = ref({
  create_allowed: false,
  update_allowed: false,
  delete_allowed: false,
});
const errorMessages = ref({ addTodo: null });

/** props 정의 및 검증 */
const props = defineProps({
  userId: {
    type: Number,
    required: true,
    default: 0,
    validator: (value) => !isNaN(parseInt(value)),
  },
});

/** 할 일 목록 및 권한 정보를 가져오는 함수 */
const fetchTodos = async () => {
  try {
    const response = await axios.get(`/api/user/${props.userId}/todos`);
    todos.value = response.data.todos;
    Object.assign(userPermissions.value, response.data.permissions);
  } catch (error) {
    console.error(error);
  }
};

/** 새로운 할 일을 추가하는 함수 */
const addTodo = async () => {
  if (!userPermissions.value.create_allowed) return;

  if (!newTodo.value.trim()) {
    errorMessages.value.addTodo = "할 일을 입력해주세요.";
    return;
  }

  try {
    const response = await axios.post(`/api/user/${props.userId}/todos`, {
      title: newTodo.value,
    });
    todos.value.push(response.data);
    newTodo.value = "";
    errorMessages.value.addTodo = null;
  } catch (error) {
    console.error(error);
    errorMessages.value.addTodo = "할 일 추가 오류";
  }
};

/** 할 일의 완료 상태를 업데이트하는 함수 */
const updateTodo = async (todo) => {
  if (!userPermissions.value.update_allowed) return;
  try {
    await axios.put(`/api/user/${props.userId}/todos/${todo.id}`, {
      completed: todo.completed,
    });
  } catch (error) {
    console.error(error);
  }
};

/** 할 일을 수정 모드로 전환하는 함수 */
const editTodo = (todo) => {
  if (!userPermissions.value.update_allowed) return;
  todo.editing = true;
  todo.updatedTitle = todo.title;
};

/** 수정된 할 일을 저장하는 함수 */
const saveUpdatedTodo = async (todo) => {
  if (!userPermissions.value.update_allowed) return;
  try {
    await axios.put(`/api/user/${props.userId}/todos/${todo.id}`, {
      title: todo.updatedTitle,
      completed: todo.completed,
    });
    todo.title = todo.updatedTitle;
    todo.editing = false;
  } catch (error) {
    console.error(error);
  }
};

/** 할 일을 삭제하는 함수 */
const deleteTodo = async (todo) => {
  if (!userPermissions.value.delete_allowed) return;
  try {
    await axios.delete(`/api/user/${props.userId}/todos/${todo.id}`);
    todos.value = todos.value.filter((t) => t.id !== todo.id);
  } catch (error) {
    console.error(error);
  }
};

/** 로그아웃 처리 함수 */
const logout = async () => {
  try {
    const token = VueCookieNext.getCookie("token");
    if (token) {
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    }

    await axios.post("/api/logout");
    VueCookieNext.removeCookie("token");
    VueCookieNext.removeCookie("isAdmin");
    router.push("/");
  } catch (error) {
    console.error("로그아웃 실패:", error);
  }
};

/** 관리자 여부를 계산하는 computed 속성 */
const isAdmin = computed(() => {
  const adminCookie = VueCookieNext.getCookie("isAdmin");
  return adminCookie === "true";
});

/** 관리자 대시보드로 이동하는 함수 */
const goToAdminDashboard = () => {
  router.push("/admin");
};

/** 폴링 간격 정의 */
const pollInterval = 5000; // 폴링 간격을 5초로 설정
let poller = null; // 폴링을 위한 변수

/** 폴링을 시작하는 함수 */
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

/** 컴포넌트 마운트 시 폴링 시작 */
onMounted(() => {
  const token = VueCookieNext.getCookie("token");

  if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    fetchTodos();
    startPolling();
  } else {
    router.push("/");
  }

  window.addEventListener("beforeunload", handleLogout);
});

/** 컴포넌트 언마운트 시 폴링 중단 */
onUnmounted(() => {
  stopPolling();
  window.removeEventListener("beforeunload", handleLogout);
});

const handleLogout = async () => {
  await logout();
};
</script>
