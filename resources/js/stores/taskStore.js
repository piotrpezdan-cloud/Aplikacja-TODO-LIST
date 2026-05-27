import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

export const useTaskStore = defineStore('tasks', () => {
  const tasks = ref([]);
  const categories = ref([]);
  const projects = ref([]);
  const comments = ref({});
  const isLoading = ref(false);
  const error = ref(null);

  const unwrapList = (payload) => payload.data?.data || payload.data || payload;
  const unwrapItem = (payload) => payload.data || payload;

  const fetchTasks = async (filters = {}) => {
    isLoading.value = true;
    error.value = null;
    try {
      const { data } = await api.get('/tasks', { params: filters });
      tasks.value = unwrapList(data);
    } catch (err) {
      error.value = 'Błąd przy pobieraniu zadań';
    } finally {
      isLoading.value = false;
    }
  };

  const fetchTask = async (id) => {
    const { data } = await api.get(`/tasks/${id}`);
    return unwrapItem(data);
  };

  const fetchCategories = async () => {
    const { data } = await api.get('/categories');
    categories.value = unwrapList(data);
  };

  const fetchProjects = async () => {
    const { data } = await api.get('/projects');
    projects.value = unwrapList(data);
  };

  const createProject = async (projectData) => {
    const { data } = await api.post('/projects', projectData);
    projects.value.push(unwrapItem(data));
    return unwrapItem(data);
  };

  const createTask = async (taskData) => {
    try {
      const { data } = await api.post('/tasks', { priority: 'medium', ...taskData });
      tasks.value.unshift(unwrapItem(data));
      return true;
    } catch (err) {
      error.value = 'Błąd przy tworzeniu zadania';
      return false;
    }
  };

  const updateTask = async (id, taskData) => {
    try {
      const { data } = await api.put(`/tasks/${id}`, taskData);
      const index = tasks.value.findIndex((task) => task.id === Number(id));
      if (index >= 0) tasks.value[index] = unwrapItem(data);
      return true;
    } catch (err) {
      error.value = 'Błąd przy aktualizacji zadania';
      return false;
    }
  };

  const deleteTask = async (id) => {
    try {
      await api.delete(`/tasks/${id}`);
      tasks.value = tasks.value.filter((task) => task.id !== id);
      return true;
    } catch (err) {
      error.value = 'Błąd przy usuwaniu zadania';
      return false;
    }
  };

  const changeStatus = async (id, status) => {
    const { data } = await api.patch(`/tasks/${id}/status`, { status });
    const index = tasks.value.findIndex((task) => task.id === id);
    if (index >= 0) tasks.value[index] = unwrapItem(data);
  };

  const fetchComments = async (taskId) => {
    const { data } = await api.get(`/tasks/${taskId}/comments`);
    comments.value[taskId] = unwrapList(data);
  };

  const addComment = async (taskId, content) => {
    const { data } = await api.post(`/tasks/${taskId}/comments`, { content });
    comments.value[taskId] = [...(comments.value[taskId] || []), unwrapItem(data)];
  };

  return {
    tasks,
    categories,
    projects,
    comments,
    isLoading,
    error,
    fetchTasks,
    fetchTask,
    fetchCategories,
    fetchProjects,
    createProject,
    createTask,
    updateTask,
    deleteTask,
    changeStatus,
    fetchComments,
    addComment,
  };
});
