<template>
  <div class="todo-container">
    <h1>Todo List</h1>
    <div class="add-todo">
      <input
        type="text"
        v-model="newTodo"
        @keyup.enter="addTodo"
        placeholder="Add new todo..."
      />
      <button @click="addTodo">Add</button>
    </div>

    <ul class="todo-list">
      <li
        v-for="todo in todos"
        :key="todo.id"
        :class="{ completed: todo.completed }"
      >
        <input
          type="checkbox"
          v-model="todo.completed"
          @change="updateTodo(todo)"
        />
        <span v-if="!todo.editing">{{ todo.title }}</span>
        <input v-if="todo.editing" type="text" v-model="todo.updatedTitle" />
        <button v-if="!todo.editing" @click="editTodo(todo)">Edit</button>
        <button v-if="todo.editing" @click="saveUpdatedTodo(todo)">
          Update
        </button>
        <button @click="deleteTodo(todo)">Delete</button>
      </li>
    </ul>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";

export default {
  setup() {
    const todos = ref([]);
    const newTodo = ref("");

    const fetchTodos = async () => {
      try {
        const response = await axios.get("/api/todos");
        todos.value = response.data;
      } catch (error) {
        console.error(error);
      }
    };

    const addTodo = async () => {
      if (newTodo.value.trim()) {
        try {
          const response = await axios.post("/api/todos", {
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
        await axios.put(`/api/todos/${todo.id}`, {
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
          await axios.put(`/api/todos/${todo.id}`, {
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
        await axios.delete(`/api/todos/${todo.id}`);
        todos.value = todos.value.filter((t) => t.id !== todo.id);
      } catch (error) {
        console.error(error);
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
    };
  },
};
</script>

<style>
.todo-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
}

.add-todo {
  margin-bottom: 20px;
}

.add-todo input[type="text"] {
  padding: 10px;
  width: 70%;
  margin-right: 10px;
}

.add-todo button {
  padding: 10px 20px;
  cursor: pointer;
}

.todo-list {
  list-style: none;
  padding: 0;
}

.todo-list li {
  background: #f9f9f9;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 4px;
  display: flex;
  align-items: center;
}

.todo-list li.completed {
  text-decoration: line-through;
  opacity: 0.7;
}

.todo-list input[type="checkbox"] {
  margin-right: 10px;
}
</style>