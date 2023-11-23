<template>
  <div>
    <h1>Admin Dashboard</h1>
    <div v-for="user in users" :key="user.id">
      <h2>{{ user.name }}</h2>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const users = ref([]);

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
