<template>
  <div class="form-container">
    <h2>Login</h2>
    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />
      <button type="submit">Login</button>
    </form>
    <button @click="goToRegister">Register</button>
  </div>
</template>

<script>
import axios from "axios";
import { reactive, toRefs } from "vue";
import { useRouter } from "vue-router";
import { store } from "../store";

export default {
  setup() {
    const loginForm = reactive({
      email: "",
      password: "",
    });

    const router = useRouter();

    const login = async () => {
      try {
        const response = await axios.post("/api/login", loginForm);
        store.user = { ...response.data.user, registered: true };

        const userId = response.data.user.id;
        router.push(`/${userId}/todos`);
      } catch (error) {
        console.error("Login failed:", error);
      }
    };

    const goToRegister = () => {
      router.push("/register");
    };

    return {
      ...toRefs(loginForm),
      login,
      useRouter,
      goToRegister,
    };
  },
};
</script>

<style>
.form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
}

.form-container input[type="email"],
.form-container input[type="password"] {
  padding: 10px;
  width: 70%;
  margin-bottom: 10px;
}

.form-container button {
  padding: 10px 20px;
  cursor: pointer;
}
</style>
