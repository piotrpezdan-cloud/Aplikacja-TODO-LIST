<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">ToDo List</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="user@example.com"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Hasło</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="Hasło"
          />
        </div>

        <div v-if="auth.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
          {{ auth.error }}
        </div>

        <button
          type="submit"
          :disabled="auth.isLoading"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
        >
          {{ auth.isLoading ? 'Logowanie...' : 'Zaloguj się' }}
        </button>
      </form>

      <p class="text-center text-gray-600 mt-6">
        Nie masz konta?
        <router-link to="/user/register" class="text-blue-600 hover:underline font-semibold">
          Zarejestruj się
        </router-link>
      </p>

      <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-500 text-center mb-3">Testowe konta:</p>
        <button
          type="button"
          @click="quickLogin('jacek91@example.net', 'password')"
          class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-2 px-4 rounded-lg mb-2 transition"
        >
          Test: jacek91@example.net
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

const router = useRouter();
const auth = useAuthStore();

const form = reactive({
  email: '',
  password: '',
});

const handleLogin = async () => {
  const success = await auth.login(form.email, form.password);
  if (success) {
    router.push('/user/tasks');
  }
};

const quickLogin = async (email, password) => {
  form.email = email;
  form.password = password;
  const success = await auth.login(email, password);
  if (success) {
    router.push('/user/tasks');
  }
};
</script>
