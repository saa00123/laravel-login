import { createRouter, createWebHistory } from "vue-router";
import LoginForm from "../components/LoginForm.vue";
import RegisterForm from "../components/RegisterForm.vue";
import TodoList from "../components/TodoList.vue";
import AdminPage from "../components/AdminPage.vue";
import { store } from "../store";
import { VueCookieNext } from "vue-cookie-next";

// 애플리케이션의 라우팅을 정의
const routes = [
  { path: "/", component: LoginForm }, // 로그인 폼
  { path: "/register", component: RegisterForm }, // 회원가입 폼
  { path: "/:userId/todos", component: TodoList, props: true }, // 특정 사용자의 할 일 목록
  { path: "/admin", component: AdminPage, meta: { requiresAdmin: true } }, // 관리자 페이지
];

// Vue Router 인스턴스 생성
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 글로벌 네비게이션 가드 설정
router.beforeEach((to, from, next) => {
  const isAdmin = VueCookieNext.getCookie("isAdmin") === "true"; // 관리자 여부 확인

  // 관리자만 접근 가능한 경로에 대한 접근 권한 체크
  if (to.matched.some((record) => record.meta.requiresAdmin) && !isAdmin) {
    next({ path: "/" }); // 관리자가 아닐 경우 로그인 페이지로 리디렉션
  } else {
    next(); // 접근 허용
  }
});

export default router;
