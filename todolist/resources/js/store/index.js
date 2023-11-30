import { reactive } from "vue";

// 반응형 상태 관리 객체 생성
export const store = reactive({
  user: null, // 현재 로그인한 사용자 데이터

  // 사용자 로그인 처리 메소드
  loginUser(userData) {
    this.user = userData;
    localStorage.setItem("user", JSON.stringify(userData)); // 로컬 스토리지에 사용자 데이터 저장
  },

  // 사용자 로그아웃 처리 메소드
  logoutUser() {
    this.user = null;
    localStorage.removeItem("user"); // 로컬 스토리지에서 사용자 데이터 제거
  },

  // 관리자 여부를 반환하는 계산된 속성
  get isAdmin() {
    return this.user?.is_admin ?? false; // 사용자가 관리자인지 여부 반환
  },
});

// 스토어 초기화 함수
function initializeStore() {
  const userData = localStorage.getItem("user"); // 로컬 스토리지에서 사용자 데이터 가져오기
  if (userData) {
    store.loginUser(JSON.parse(userData)); // 사용자 데이터가 있으면 로그인 처리
  }
}

initializeStore(); // 스토어 초기화 실행
