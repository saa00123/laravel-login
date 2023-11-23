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
      </h2>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const users = ref([]);
const router = useRouter();

const fetchUsers = async () => {
  try {
    const response = await axios.get("/api/admin");
    users.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

onMounted(fetchUsers);
</script>
