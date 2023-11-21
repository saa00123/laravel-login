<template>
    <form @submit.prevent="login">
        <input
            v-model="credentials.nickname"
            type="text"
            placeholder="Nickname"
        />
        <input
            v-model="credentials.password"
            type="password"
            placeholder="Password"
        />
        <button type="submit">Login</button>
        <p v-if="errorMessage">{{ errorMessage }}</p>
    </form>
</template>

<script>
import { reactive, ref } from "vue";
import axios from "axios";

export default {
    setup() {
        const credentials = reactive({
            nickname: "",
            password: "",
        });
        const errorMessage = ref("");

        const login = async () => {
            try {
                const response = await axios.post("/api/login", credentials);
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    errorMessage.value =
                        "잘못된 사용자 이름이나 비밀번호입니다.";
                } else {
                    errorMessage.value = "로그인 중 오류가 발생했습니다.";
                }
            }
        };

        return { credentials, errorMessage, login };
    },
};
</script>
