<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>
    <div v-for="user in users" :key="user.id" class="user-entry mb-4">
      <div class="user-info">
        <h2 class="text-2xl font-bold text-gray-800">
          <router-link :to="`/${user.id}/todos`">{{ user.name }}</router-link>
          <span class="ml-4">({{ user.todoCount }} Todos)</span>
        </h2>
      </div>
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
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

const users = ref([]);
const router = useRouter();

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
  "bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded":
    allowed,
  "bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded":
    !allowed,
});

onMounted(fetchUsers);
</script>
