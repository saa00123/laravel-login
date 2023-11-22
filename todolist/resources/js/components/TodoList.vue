<template>
  <div class="max-w-md mx-auto my-10 bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-4xl font-bold text-gray-800 mb-6">Todo List</h1>
    <div class="flex items-center mb-4">
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
        class="flex items-center justify-between p-3 border rounded"
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
            class="form-input"
          />
        </div>
        <div class="flex space-x-2">
          <button @click="editTodo(todo)" class="border-2 rounded-lg p-1">
            Edit
          </button>
          <button @click="deleteTodo(todo)" class="border-2 rounded-lg p-1">
            Delete
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default {
  props: {
    userId: {
      type: Number,
      required: true,
      default: () => 0,
      validator: (value) => !isNaN(parseInt(value)),
    },
  },
  setup(props) {
    const todos = ref([]);
    const newTodo = ref("");
    const router = useRouter();

    const fetchTodos = async () => {
      try {
        const response = await axios.get(`/api/user/${props.userId}/todos`);
        todos.value = response.data;
      } catch (error) {
        console.error(error);
      }
    };

    const addTodo = async () => {
      if (newTodo.value.trim()) {
        try {
          const response = await axios.post(`/api/user/${props.userId}/todos`, {
            title: newTodo.value,
          });
          todos.value.push(response.data);
          newTodo.value = "";
        } catch (error) {
          console.error(error);
        }
      }
    };

    const updateTodo = async (todo) => {
      try {
        await axios.put(`/api/user/${props.userId}/todos/${todo.id}`, {
          completed: todo.completed,
        });
      } catch (error) {
        console.error(error);
      }
    };

    const editTodo = (todo) => {
      todo.editing = true;
      todo.updatedTitle = todo.title;
    };

    const saveUpdatedTodo = async (todo) => {
      if (todo.updatedTitle.trim()) {
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
      } else {
        todo.editing = false;
      }
    };

    const deleteTodo = async (todo) => {
      try {
        await axios.delete(`/api/user/${props.userId}/todos/${todo.id}`);
        todos.value = todos.value.filter((t) => t.id !== todo.id);
      } catch (error) {
        console.error(error);
      }
    };

    const logout = async () => {
      try {
        await axios.post("/api/logout");
        router.push("/");
      } catch (error) {
        console.error("Logout failed:", error);
      }
    };

    onMounted(fetchTodos);

    return {
      todos,
      newTodo,
      addTodo,
      updateTodo,
      editTodo,
      saveUpdatedTodo,
      deleteTodo,
      logout,
    };
  },
};
</script>
