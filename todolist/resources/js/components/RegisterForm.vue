<template>
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Register</h2>
    <form @submit.prevent="register" class="space-y-4">
      <input
        v-model="name"
        type="text"
        placeholder="Name"
        class="border-2 rounded-lg p-1"
      />
      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="border-2 rounded-lg p-1 ml-5"
      />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        class="border-2 rounded-lg p-1 w-full"
      />
      <button type="submit" class="border-2 rounded-lg p-1 w-full">
        Register
      </button>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

export default {
  setup() {
    const router = useRouter();
    const name = ref("");
    const email = ref("");
    const password = ref("");

    const register = async () => {
      try {
        const response = await axios.post("/api/register", {
          name: name.value,
          email: email.value,
          password: password.value,
        });
        router.push("/");
      } catch (error) {
        console.error("Registration failed:", error);
      }
    };

    return {
      name,
      email,
      password,
      register,
    };
  },
};
</script>
