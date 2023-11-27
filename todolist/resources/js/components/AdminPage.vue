<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>
    <div v-for="user in users" :key="user.id" class="user-entry">
      <div class="user-info">
        <h2 class="text-2xl font-bold text-gray-800">
          <router-link :to="`/${user.id}/todos`">{{ user.name }}</router-link>
          <span class="ml-4">({{ user.todoCount }} Todos)</span>
        </h2>
      </div>
      <div class="crud-buttons">
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
    users.value = response.data.filter((user) => !user.is_admin);
  } catch (error) {
    console.error(error);
  }
};

const togglePermission = async (user, permissionType) => {
  user[`${permissionType}_allowed`] = !user[`${permissionType}_allowed`];
  try {
    await axios.put(`/api/admin/update-crud-permission/${user.id}`, {
      [`${permissionType}_allowed`]: user[`${permissionType}_allowed`],
    });
  } catch (error) {
    console.error("Error toggling permission:", error);
  }
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

const buttonClass = (allowed) => ({
  "green-border": allowed,
  "red-border": !allowed,
});

onMounted(fetchUsers);
</script>

<style>
.green-border {
  border: 2px solid green;
}
.red-border {
  border: 2px solid red;
}
</style>
