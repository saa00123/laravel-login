<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Todo List</h1>
    <button @click="logout" class="border-2 rounded-lg p-1 w-full mb-6">
      Logout
    </button>
    <button
      v-if="isAdmin"
      @click="goToAdminDashboard"
      class="border-2 rounded-lg p-1 w-full mb-6"
    >
      Go to Admin Dashboard
    </button>
    <div v-if="userPermissions.create_allowed" class="flex items-center mb-4">
      <input
        type="text"
        class="border-2 rounded-lg p-1 w-full mr-2"
        v-model="newTodo"
        @keyup.enter="addTodo"
        placeholder="Add new todo..."
      />
      <button class="border-2 rounded-lg p-1" @click="addTodo">Add</button>
    </div>
    <ul class="space-y-3">
      <li
        v-for="todo in todos"
        :key="todo.id"
        :class="{ 'line-through text-gray-500': todo.completed }"
        class="flex border-2 rounded-lg items-center justify-between p-3"
      >
        <div class="flex items-center">
          <input
            type="checkbox"
            v-model="todo.completed"
            @change="updateTodo(todo)"
            class="mr-3"
          />
          <span v-if="!todo.editing">{{ todo.title }}</span>
          <input
            v-if="todo.editing"
            type="text"
            v-model="todo.updatedTitle"
            @blur="saveUpdatedTodo(todo)"
            @keyup.enter="saveUpdatedTodo(todo)"
            class="border-2 rounded-lg p-1"
          />
        </div>
        <div class="flex space-x-2">
          <button
            v-if="userPermissions.update_allowed"
            @click="editTodo(todo)"
            class="border-2 rounded-lg p-1"
          >
            Edit
          </button>
          <button
            v-if="userPermissions.delete_allowed"
            @click="deleteTodo(todo)"
            class="border-2 rounded-lg p-1"
          >
            Delete
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { VueCookieNext } from "vue-cookie-next";

const todos = ref([]);
const newTodo = ref("");
const router = useRouter();
const userPermissions = ref({
  create_allowed: false,
  update_allowed: false,
  delete_allowed: false,
});

const props = defineProps({
  userId: {
    type: Number,
    required: true,
    default: 0,
    validator: (value) => !isNaN(parseInt(value)),
  },
});

const fetchTodos = async () => {
  try {
    const response = await axios.get(`/api/user/${props.userId}/todos`);
    todos.value = response.data.todos;
    Object.assign(userPermissions.value, response.data.permissions);
  } catch (error) {
    console.error(error);
  }
};

const addTodo = async () => {
  if (!userPermissions.value.create_allowed) return;
  try {
    const response = await axios.post(`/api/user/${props.userId}/todos`, {
      title: newTodo.value,
    });
    todos.value.push(response.data);
    newTodo.value = "";
  } catch (error) {
    console.error(error);
  }
};

const updateTodo = async (todo) => {
  if (!userPermissions.value.update_allowed) return;
  try {
    await axios.put(`/api/user/${props.userId}/todos/${todo.id}`, {
      completed: todo.completed,
    });
  } catch (error) {
    console.error(error);
  }
};

const editTodo = (todo) => {
  if (!userPermissions.value.update_allowed) return;
  todo.editing = true;
  todo.updatedTitle = todo.title;
};

const saveUpdatedTodo = async (todo) => {
  if (!userPermissions.value.update_allowed) return;
  try {
    await axios.put(`/api/user/${props.userId}/todos/${todo.id}`, {
      title: todo.updatedTitle,
      completed: todo.completed,
    });
    todo.title = todo.updatedTitle;
    todo.editing = false;
  } catch (error) {
    console.error(error);
  }
};

const deleteTodo = async (todo) => {
  if (!userPermissions.value.delete_allowed) return;
  try {
    await axios.delete(`/api/user/${props.userId}/todos/${todo.id}`);
    todos.value = todos.value.filter((t) => t.id !== todo.id);
  } catch (error) {
    console.error(error);
  }
};

const logout = async () => {
  try {
    VueCookieNext.removeCookie("token");
    await axios.post("/api/logout");
    router.push("/");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};

const isAdmin = computed(() => {
  const adminCookie = VueCookieNext.getCookie("isAdmin");
  return adminCookie === "true";
});

const goToAdminDashboard = () => {
  router.push("/admin");
};

onMounted(fetchTodos);
</script>

<style>
.green-border {
  border: 2px solid green;
}
.red-border {
  border: 2px solid red;
}
</style>
