<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-40">
      <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <router-link to="/user/tasks" class="text-2xl font-bold text-blue-600">
          ToDo List
        </router-link>

        <div v-if="auth.isAuthenticated()" class="flex items-center gap-6">
          <span class="text-gray-700">{{ auth.user.first_name }} {{ auth.user.last_name }}</span>
          <router-link to="/user/profile" class="text-blue-600 hover:text-blue-800 font-semibold transition">
            Profil
          </router-link>
          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition"
          >
            Wyloguj
          </button>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

const router = useRouter();
const auth = useAuthStore();

const handleLogout = async () => {
  await auth.logout();
  router.push('/user/login');
};
</script>

<style scoped>
#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell,
    'Helvetica Neue', sans-serif;
}
</style>
