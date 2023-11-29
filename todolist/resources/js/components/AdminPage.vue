<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>
    <div
      v-for="user in users"
      :key="user.id"
      class="user-entry mb-4 border-2 rounded-lg p-1 w-full mb-6"
    >
      <div class="user-info">
        <h2 class="text-2xl font-bold text-gray-800">
          <router-link :to="`/${user.id}/todos`">{{ user.name }}</router-link>
          <span class="ml-4 mr-4">({{ user.todoCount }} Todos)</span>
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
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";
import { store } from "../store";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

const users = ref([]);
const router = useRouter();
const loading = ref(true);

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

const buttonClass = (allowed) => ({
  "bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded":
    allowed,
  "bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded":
    !allowed,
});

const subscribeToUserUpdates = () => {
  window.Echo = new Echo({
    broadcaster: "pusher",
    key: "d530c4d0851df35c4452",
    cluster: "ap3",
    encrypted: true,
  });

  window.Echo.join("users")
    .here((usersList) => {
      users.value = usersList;
    })
    .listen("UserOnlineStatusChanged", (e) => {
      const updatedUser = e.user;
      const index = users.value.findIndex((u) => u.id === updatedUser.id);
      if (index !== -1) {
        users.value[index] = updatedUser;
      }
    });
};

onMounted(async () => {
  const token = VueCookieNext.getCookie("token");
  if (!token) {
    router.push("/");
    return;
  }

  try {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    const response = await axios.get("/api/user");

    if (response.data && response.data.is_admin) {
      fetchUsers();
      subscribeToUserUpdates();
    } else {
      console.log("Access denied. Admin privileges are required.");
    }
  } catch (error) {
    console.error("Error fetching user data:", error);
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  if (window.Echo) {
    window.Echo.leave("users");
  }
});
</script>
