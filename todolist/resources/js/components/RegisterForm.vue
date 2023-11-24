<template>
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Register</h2>
    <form @submit.prevent="register" class="space-y-4">
      <div>
        <input
          v-model="name"
          type="text"
          placeholder="Name"
          class="border-2 rounded-lg p-1 w-full"
        />
        <div v-if="errorMessages.name" class="text-red-500">
          {{ errorMessages.name }}
        </div>
      </div>
      <div>
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="border-2 rounded-lg p-1 w-full"
        />
        <div v-if="errorMessages.email" class="text-red-500">
          {{ errorMessages.email }}
        </div>
      </div>
      <div>
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="border-2 rounded-lg p-1 w-full"
        />
        <div v-if="errorMessages.password" class="text-red-500">
          {{ errorMessages.password }}
        </div>
      </div>
      <button type="submit" class="border-2 rounded-lg p-1 w-full">
        Register
      </button>
    </form>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const name = ref("");
const email = ref("");
const password = ref("");
const errorMessages = reactive({
  name: null,
  email: null,
  password: null,
});

const register = async () => {
  try {
    const response = await axios.post("/api/register", {
      name: name.value,
      email: email.value,
      password: password.value,
    });

    errorMessages.name = null;
    errorMessages.email = null;
    errorMessages.password = null;

    router.push("/");
  } catch (error) {
    if (error.response && error.response.data.errors) {
      errorMessages.name = error.response.data.errors.name || null;
      errorMessages.email = error.response.data.errors.email || null;
      errorMessages.password = error.response.data.errors.password || null;
    } else {
      console.error("Registration failed:", error);
    }
  }
};
</script>
