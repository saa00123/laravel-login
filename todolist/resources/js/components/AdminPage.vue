<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <div
      v-for="user in users"
      :key="user.id"
      class="border-2 rounded-lg p-1 mb-4"
    >
      <h2 class="text-2xl font-bold text-gray-800">
        <router-link :to="`/${user.id}/todos`">{{ user.name }}</router-link>
        <span class="ml-4">({{ user.todoCount }} Todos)</span>
      </h2>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { VueCookieNext } from "vue-cookie-next";

// 사용자 목록을 위한 반응형 참조
const users = ref([]);

// 특정 사용자 ID에 대한 할 일 수를 가져오는 함수
const fetchTodosByUserId = async (userId) => {
  try {
    const response = await axios.get(`/api/user/${userId}/todos`);
    return response.data.length;
  } catch (error) {
    console.error(error);
    return 0;
  }
};

const fetchUsers = async () => {
  try {
    const response = await axios.get("/api/admin");
    let usersData = response.data;

    const isAdmin = VueCookieNext.getCookie("isAdmin") === "true";
    const adminId = isAdmin
      ? parseInt(VueCookieNext.getCookie("adminId"))
      : null;

    usersData = usersData.filter((user) => {
      return (
        user.name.toLowerCase() !== "admin" && (!isAdmin || user.id !== adminId)
      );
    });

    for (const user of usersData) {
      user.todoCount = await fetchTodosByUserId(user.id);
    }

    users.value = usersData;
  } catch (error) {
    console.error(error);
  }
};

onMounted(fetchUsers);
</script>
