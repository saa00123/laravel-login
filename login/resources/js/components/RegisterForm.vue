<template>
    <form @submit.prevent="register">
        <input v-model="user.name" type="text" placeholder="Name" />
        <input v-model="user.nickname" type="text" placeholder="Nickname" />
        <input v-model="user.password" type="password" placeholder="Password" />
        <input v-model="user.email" type="email" placeholder="Email" />
        <input v-model="user.phone" type="text" placeholder="Phone" />
        <button type="submit">Register</button>
        <p v-if="errorMessage">{{ errorMessage }}</p>
    </form>
</template>

<script>
import { reactive, ref } from "vue";
import axios from "axios";

export default {
    setup() {
        const user = reactive({
            name: "",
            nickname: "",
            password: "",
            email: "",
            phone: "",
        });
        const errorMessage = ref("");

        const register = async () => {
            try {
                const response = await axios.post("/api/register", user);
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 400) {
                        errorMessage.value =
                            "중복된 사용자 이름이나 이메일입니다.";
                    } else if (error.response.status === 422) {
                        errorMessage.value = "입력 데이터가 올바르지 않습니다.";
                    } else {
                        errorMessage.value = "회원가입 중 오류가 발생했습니다.";
                    }
                }
            }
        };

        return { user, errorMessage, register };
    },
};
</script>
