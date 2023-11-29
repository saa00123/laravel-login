import { reactive } from "vue";

export const store = reactive({
  user: null,

  loginUser(userData) {
    this.user = userData;
    localStorage.setItem("user", JSON.stringify(userData));
  },

  logoutUser() {
    this.user = null;
    localStorage.removeItem("user");
  },

  get isAdmin() {
    return this.user?.is_admin ?? false;
  },
});

function initializeStore() {
  const userData = localStorage.getItem("user");
  if (userData) {
    store.loginUser(JSON.parse(userData));
  }
}

initializeStore();
