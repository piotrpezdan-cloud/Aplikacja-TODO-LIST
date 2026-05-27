<template>
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-3xl font-bold text-gray-800">Moje zadania</h1>
      <router-link to="/user/tasks/new" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg text-center">
        Nowe zadanie
      </router-link>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4 grid gap-3 md:grid-cols-5">
      <select v-model="filters.status" @change="reload" class="form-input">
        <option value="">Wszystkie statusy</option>
        <option value="new">Nowe</option>
        <option value="in_progress">W trakcie</option>
        <option value="done">Ukończone</option>
        <option value="archived">Zarchiwizowane</option>
      </select>
      <select v-model="filters.priority" @change="reload" class="form-input">
        <option value="">Każdy priorytet</option>
        <option value="low">Niski</option>
        <option value="medium">Średni</option>
        <option value="high">Wysoki</option>
      </select>
      <select v-model="filters.category_id" @change="reload" class="form-input">
        <option value="">Każda kategoria</option>
        <option v-for="category in tasks.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
      </select>
      <select v-model="filters.project_id" @change="reload" class="form-input">
        <option value="">Każdy projekt</option>
        <option v-for="project in tasks.projects" :key="project.id" :value="project.id">{{ project.name }}</option>
      </select>
      <input v-model="filters.search" @keyup.enter="reload" class="form-input" placeholder="Szukaj" />
    </div>

    <p v-if="tasks.isLoading" class="text-gray-600">Ładowanie zadań...</p>
    <p v-else-if="tasks.tasks.length === 0" class="text-center text-gray-600 bg-white border rounded-lg py-10">Brak zadań.</p>

    <div v-else class="grid gap-4">
      <article v-for="task in tasks.tasks" :key="task.id" class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <h2 class="text-xl font-semibold text-gray-900">{{ task.title }}</h2>
              <span class="badge">{{ statusLabel(task.status) }}</span>
              <span class="badge">{{ priorityLabel(task.priority) }}</span>
            </div>
            <p v-if="task.description" class="text-gray-600">{{ task.description }}</p>
            <div class="flex flex-wrap gap-2 text-sm text-gray-600">
              <span v-if="task.category">{{ task.category.name }}</span>
              <span v-if="task.project">{{ task.project.name }}</span>
              <span v-if="task.due_date">Termin: {{ formatDate(task.due_date) }}</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <select :value="task.status" @change="tasks.changeStatus(task.id, $event.target.value)" class="form-input">
              <option value="new">Nowe</option>
              <option value="in_progress">W trakcie</option>
              <option value="done">Ukończone</option>
              <option value="archived">Zarchiwizowane</option>
            </select>
            <router-link :to="`/user/tasks/${task.id}/edit`" class="btn-secondary">Edytuj</router-link>
            <button @click="remove(task.id)" class="btn-danger">Usuń</button>
          </div>
        </div>

        <div class="mt-4 border-t pt-4">
          <button @click="toggleComments(task.id)" class="text-blue-700 font-semibold text-sm">Komentarze</button>
          <div v-if="openComments === task.id" class="mt-3 space-y-3">
            <div v-for="comment in tasks.comments[task.id] || []" :key="comment.id" class="bg-gray-50 rounded p-3 text-sm">
              {{ comment.content }}
            </div>
            <form @submit.prevent="submitComment(task.id)" class="flex gap-2">
              <input v-model="commentText" class="form-input flex-1" placeholder="Dodaj komentarz" />
              <button class="bg-blue-600 text-white px-4 rounded-lg">Dodaj</button>
            </form>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useTaskStore } from '../stores/taskStore';

const tasks = useTaskStore();
const openComments = ref(null);
const commentText = ref('');
const filters = reactive({ status: '', priority: '', category_id: '', project_id: '', search: '' });

const cleanFilters = () => Object.fromEntries(Object.entries(filters).filter(([, value]) => value !== ''));
const reload = () => tasks.fetchTasks(cleanFilters());

const remove = async (id) => {
  if (confirm('Usunąć zadanie?')) await tasks.deleteTask(id);
};

const toggleComments = async (taskId) => {
  openComments.value = openComments.value === taskId ? null : taskId;
  if (openComments.value && !tasks.comments[taskId]) await tasks.fetchComments(taskId);
};

const submitComment = async (taskId) => {
  if (!commentText.value.trim()) return;
  await tasks.addComment(taskId, commentText.value.trim());
  commentText.value = '';
};

const statusLabel = (status) => ({ new: 'Nowe', in_progress: 'W trakcie', done: 'Ukończone', archived: 'Zarchiwizowane' }[status] || status);
const priorityLabel = (priority) => ({ low: 'Niski', medium: 'Średni', high: 'Wysoki' }[priority] || priority);
const formatDate = (date) => new Date(date).toLocaleDateString('pl-PL');

onMounted(async () => {
  await Promise.all([tasks.fetchCategories(), tasks.fetchProjects()]);
  await reload();
});
</script>

<style scoped>
.form-input { border: 1px solid #d1d5db; border-radius: 8px; padding: 0.5rem 0.75rem; background: white; }
.badge { background: #eef2ff; color: #3730a3; border-radius: 999px; padding: 0.2rem 0.6rem; font-size: 0.8rem; }
.btn-secondary { border: 1px solid #d1d5db; border-radius: 8px; padding: 0.5rem 0.75rem; color: #1f2937; }
.btn-danger { border-radius: 8px; padding: 0.5rem 0.75rem; color: white; background: #dc2626; }
</style>
