<template>
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Login</h2>
    <form @submit.prevent="login" class="space-y-4">
      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="border-2 rounded-lg p-1"
      />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        class="border-2 rounded-lg p-1 ml-5"
      />
      <button type="submit" class="border-2 rounded-lg p-1 w-full">
        Login
      </button>
    </form>
    <button @click="goToRegister" class="border-2 rounded-lg p-1 w-full mt-4">
      Register
    </button>
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, toRefs } from "vue";
import { useRouter } from "vue-router";
import { store } from "../store";
import { VueCookieNext } from "vue-cookie-next";

const loginForm = reactive({
  email: "",
  password: "",
});

const { email, password } = toRefs(loginForm);
const router = useRouter();

const login = async () => {
  try {
    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
    });
    if (response.data.access_token) {
      VueCookieNext.setCookie("token", response.data.access_token, {
        expires: "1d",
      });

      axios.defaults.headers.common[
        "Authorization"
      ] = `Bearer ${response.data.access_token}`;

      if (response.data.user.is_admin) {
        VueCookieNext.setCookie("userRole", "admin", { expires: "1d" });
        router.push("/admin");
      } else {
        VueCookieNext.removeCookie("userRole");
        const userId = response.data.user.id;
        router.push(`/${userId}/todos`);
      }
    }

    store.user = { ...response.data.user, registered: true };
  } catch (error) {
    console.error("Login failed:", error);
  }
};

const goToRegister = () => {
  router.push("/register");
};
</script>
