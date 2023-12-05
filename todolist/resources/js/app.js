import { createApp } from "vue";
import App from "./App.vue";
import router from "./router/router";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import "vue-cookie-next";
import "../css/app.css";

// Axios 전역 설정
axios.defaults.withCredentials = true; // 쿠키 전송을 위한 설정
const token = localStorage.getItem("token"); // 로컬 스토리지에서 토큰 가져오기

// 토큰이 존재하면 Axios 기본 헤더에 인증 토큰 설정
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "pusher",
  key: "d530c4d0851df35c4452",
  cluster: "ap3",
});

const app = createApp(App);

// 라우터 및 애플리케이션 마운트
app.use(router).mount("#app");
