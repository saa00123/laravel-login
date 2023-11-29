import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "pusher",
  key: "d530c4d0851df35c4452",
  cluster: "ap3",
  forceTLS: true,
});
