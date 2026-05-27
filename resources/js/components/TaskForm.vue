<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ isEditing ? 'Edytuj zadanie' : 'Nowe zadanie' }}</h1>

    <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow p-6 space-y-5">
      <div>
        <label class="label" for="title">Tytuł *</label>
        <input id="title" v-model="form.title" required class="input" />
      </div>

      <div>
        <label class="label" for="description">Opis</label>
        <textarea id="description" v-model="form.description" rows="4" class="input"></textarea>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="label" for="category">Kategoria</label>
          <select id="category" v-model="form.category_id" class="input">
            <option value="">Bez kategorii</option>
            <option v-for="category in tasks.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>

        <div>
          <label class="label" for="project">Projekt</label>
          <select id="project" v-model="form.project_id" class="input">
            <option value="">Bez projektu</option>
            <option v-for="project in tasks.projects" :key="project.id" :value="project.id">{{ project.name }}</option>
          </select>
        </div>
      </div>

      <div class="bg-gray-50 border rounded-lg p-4 space-y-3">
        <label class="label" for="projectName">Nowy projekt</label>
        <div class="flex gap-2">
          <input id="projectName" v-model="newProjectName" class="input flex-1" placeholder="Nazwa projektu" />
          <button type="button" @click="addProject" class="bg-gray-900 text-white px-4 rounded-lg">Dodaj</button>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="label" for="priority">Priorytet</label>
          <select id="priority" v-model="form.priority" class="input">
            <option value="low">Niski</option>
            <option value="medium">Średni</option>
            <option value="high">Wysoki</option>
          </select>
        </div>

        <div>
          <label class="label" for="status">Status</label>
          <select id="status" v-model="form.status" class="input">
            <option value="new">Nowe</option>
            <option value="in_progress">W trakcie</option>
            <option value="done">Ukończone</option>
            <option value="archived">Zarchiwizowane</option>
          </select>
        </div>

        <div>
          <label class="label" for="dueDate">Termin</label>
          <input id="dueDate" v-model="form.due_date" type="date" :min="today" class="input" />
        </div>
      </div>

      <div v-if="tasks.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ tasks.error }}</div>

      <div class="flex gap-3">
        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
          {{ isEditing ? 'Zaktualizuj' : 'Utwórz' }}
        </button>
        <router-link to="/user/tasks" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-center">
          Anuluj
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, computed, onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';

const router = useRouter();
const route = useRoute();
const tasks = useTaskStore();
const newProjectName = ref('');
const today = new Date().toISOString().slice(0, 10);
const isEditing = computed(() => !!route.params.id);

const form = reactive({
  title: '',
  description: '',
  category_id: '',
  project_id: '',
  priority: 'medium',
  due_date: '',
  status: 'new',
});

const payload = () => ({
  title: form.title,
  description: form.description || null,
  category_id: form.category_id || null,
  project_id: form.project_id || null,
  priority: form.priority || 'medium',
  due_date: form.due_date || null,
  status: form.status || 'new',
});

const addProject = async () => {
  if (!newProjectName.value.trim()) return;
  const project = await tasks.createProject({ name: newProjectName.value.trim() });
  form.project_id = project.id;
  newProjectName.value = '';
};

const handleSubmit = async () => {
  const success = isEditing.value
    ? await tasks.updateTask(route.params.id, payload())
    : await tasks.createTask(payload());

  if (success) router.push('/user/tasks');
};

onMounted(async () => {
  await Promise.all([tasks.fetchCategories(), tasks.fetchProjects()]);

  if (isEditing.value) {
    const task = await tasks.fetchTask(route.params.id);
    Object.assign(form, {
      title: task.title,
      description: task.description || '',
      category_id: task.category_id || '',
      project_id: task.project_id || '',
      priority: task.priority || 'medium',
      due_date: task.due_date || '',
      status: task.status || 'new',
    });
  }
});
</script>

<style scoped>
.label { display: block; margin-bottom: 0.4rem; font-size: 0.875rem; font-weight: 600; color: #374151; }
.input { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 0.55rem 0.75rem; outline: none; }
.input:focus { border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15); }
</style>
