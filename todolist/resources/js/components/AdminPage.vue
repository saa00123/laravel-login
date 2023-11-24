<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>
    <div
      v-for="user in users"
      :key="user.id"
      class="border-2 rounded-lg p-1 mb-4 cursor-pointer"
      @click="navigateToUserTodos(user.id)"
    >
      <h2 class="text-2xl font-bold text-gray-800">
        {{ user.name }}
        <span class="ml-4">({{ user.todoCount }} Todos)</span>
      </h2>
      <button @click="toggleCrudPermission(user)">
        {{ user.is_crud_allowed ? "Revoke CRUD" : "Grant CRUD" }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

const users = ref([]);
const router = useRouter();

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

const navigateToUserTodos = (userId) => {
  router.push(`/${userId}/todos`);
};

const logout = async () => {
  try {
    VueCookieNext.removeCookie("token");
    await axios.post("/api/logout");
    router.push("/");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};

onMounted(fetchUsers);
</script>
