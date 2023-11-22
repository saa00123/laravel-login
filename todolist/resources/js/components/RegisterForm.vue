<template>
  <div>
    <h2>Register</h2>
    <form @submit.prevent="register">
      <input v-model="name" type="text" placeholder="Name" />
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />
      <button type="submit">Register</button>
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

<style>
.form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
}

.form-container input[type="text"],
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
