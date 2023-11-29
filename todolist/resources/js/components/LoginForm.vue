<template>
  <div
    class="form-container max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg"
  >
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Login</h2>
    <form @submit.prevent="login" class="flex-col space-y-4">
      <div class="flex-col">
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
      <div class="flex-col">
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
  errorMessages: {
    email: null,
    password: null,
  },
});

const { email, password, errorMessages } = toRefs(loginForm);
const router = useRouter();

const login = async () => {
  try {
    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
    });

    errorMessages.value.email = null;
    errorMessages.value.password = null;

    VueCookieNext.setCookie("token", response.data.access_token, {
      expires: "1d",
    });
    axios.defaults.headers.common[
      "Authorization"
    ] = `Bearer ${response.data.access_token}`;

    const user = response.data.user;
    VueCookieNext.setCookie("isAdmin", user.is_admin ? "true" : "false", {
      expires: "1d",
    });
    if (response.data.user.is_admin) {
      router.push("/admin");
    } else {
      const userId = response.data.user.id;
      router.push(`/${userId}/todos`);
    }

    if (user.is_admin) {
      router.push("/admin");
    } else {
      router.push(`/${user.id}/todos`);
    }

    store.user = { ...response.data.user, registered: true };
  } catch (error) {
    if (error.response && error.response.data.errors) {
      errorMessages.value.email = error.response.data.errors.email || null;
      errorMessages.value.password =
        error.response.data.errors.password || null;
    } else {
      console.error("Login failed:", error);
    }
  }
};

const goToRegister = () => {
  router.push("/register");
};
</script>
