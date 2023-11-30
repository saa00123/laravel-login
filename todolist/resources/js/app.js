import { createApp } from "vue";
import App from "./App.vue";
import router from "./router/router";
import axios from "axios";
import "../css/app.css";

// Axios 전역 설정
axios.defaults.withCredentials = true; // 쿠키 전송을 위한 설정
const token = localStorage.getItem("token"); // 로컬 스토리지에서 토큰 가져오기

// 토큰이 존재하면 Axios 기본 헤더에 인증 토큰 설정
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Vue 애플리케이션 생성 및 마운트
createApp(App).use(router).mount("#app");
