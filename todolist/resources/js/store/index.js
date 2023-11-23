import { reactive } from "vue";

export const store = reactive({
  user: null,

  loginUser(userData) {
    this.user = userData;
  },

  logoutUser() {
    this.user = null;
  },

  get isAdmin() {
    return this.user?.is_admin ?? false;
  },
});
