<template>
  <!-- 관리자 대시보드의 메인 템플릿 -->
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

    <!-- 온라인/오프라인 필터링 토글 버튼 -->
    <button
      @click="toggleOnlineFilter"
      class="border-2 rounded-lg p-1 w-full mb-6"
    >
      {{ showOnlineOnly ? "Show All Users" : "Show Online Only" }}
    </button>

    <!-- 로그아웃 버튼 -->
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>

    <!-- 사용자 목록 -->
    <div
      v-for="user in filteredUsers"
      :key="user.id"
      class="user-entry mb-4 border-2 rounded-lg p-1 w-full"
    >
      <div class="user-info">
        <h2 class="text-2xl font-bold text-gray-800">
          <!-- 사용자 이름과 할 일 수 표시 -->
          <router-link :to="`/${user.id}/todos`">{{ user.name }}</router-link>
          <span class="ml-4 mr-4">({{ user.todoCount }} Todos)</span>

          <!-- 온라인/오프라인 상태 표시 -->
          <span
            :class="{
              'text-green-500': user.is_online,
              'text-red-500': !user.is_online,
            }"
          >
            {{ user.is_online ? "Online" : "Offline" }}
          </span>
        </h2>
      </div>

      <!-- 사용자별 권한 조절 버튼 -->
      <div class="crud-buttons flex space-x-2 mt-2">
        <button
          :class="buttonClass(user.create_allowed)"
          @click.prevent="togglePermission(user, 'create')"
        >
          Create
        </button>
        <button
          :class="buttonClass(user.update_allowed)"
          @click.prevent="togglePermission(user, 'update')"
        >
          Update
        </button>
        <button
          :class="buttonClass(user.delete_allowed)"
          @click.prevent="togglePermission(user, 'delete')"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
/** Vue, Axios, Vue Router, Vue Cookie 라이브러리 import */
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

/** 사용자 정보 및 기타 상태를 위한 참조 변수 선언 */
const users = ref([]);
const router = useRouter();
const loading = ref(true);
const pollInterval = 5000;
let poller = null;
const showOnlineOnly = ref(true);

/** 사용자 데이터를 가져오는 함수 */
const fetchUsers = async () => {
  try {
    const response = await axios.get("/api/admin");
    const isAdmin = VueCookieNext.getCookie("isAdmin") === "true";
    const adminId = isAdmin
      ? parseInt(VueCookieNext.getCookie("adminId"))
      : null;

    users.value = response.data
      .filter(
        (user) =>
          user.name.toLowerCase() !== "admin" &&
          (!isAdmin || user.id !== adminId),
      )
      .map((user) => ({
        ...user,
        todoCount: user.todos_count,
      }));
  } catch (error) {
    console.error("Error fetching users:", error);
  }
};

/** 사용자 권한 변경 함수 */
const togglePermission = async (user, permissionType) => {
  try {
    const newPermissionStatus = !user[`${permissionType}_allowed`];

    await axios.put(`/api/admin/user/${user.id}/permissions`, {
      [`${permissionType}_allowed`]: newPermissionStatus,
    });

    user[`${permissionType}_allowed`] = newPermissionStatus;
  } catch (error) {
    console.error("Error updating permission:", error);
  }
};

/** 로그아웃 함수 */
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
    console.error("Logout failed:", error);
  }
};

/** 버튼 클래스 설정 함수 */
const buttonClass = (allowed) => ({
  "bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded":
    allowed,
  "bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded":
    !allowed,
});

/** 사용자 데이터 폴링 시작 함수 */
const startPolling = () => {
  poller = setInterval(() => {
    fetchUsers();
  }, pollInterval);
};

/** 사용자 데이터 폴링 종료 함수 */
const stopPolling = () => {
  if (poller) {
    clearInterval(poller);
  }
};

/** 온라인 상태에 따라 사용자 필터링 */
const filteredUsers = computed(() => {
  return showOnlineOnly.value
    ? users.value.filter((user) => user.is_online)
    : users.value;
});

/** 온라인/오프라인 필터링 토글 */
const toggleOnlineFilter = () => {
  showOnlineOnly.value = !showOnlineOnly.value;
};

/** 컴포넌트 마운트시 실행되는 함수 */
onMounted(async () => {
  const token = VueCookieNext.getCookie("token");
  if (!token) {
    router.push("/");
    return;
  }

  try {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    fetchUsers();
    startPolling();
  } catch (error) {
    console.error("Error during mounting:", error);
  } finally {
    loading.value = false;
  }
});

/** 컴포넌트 언마운트시 실행되는 함수 */
onUnmounted(() => {
  stopPolling();
});
</script>
