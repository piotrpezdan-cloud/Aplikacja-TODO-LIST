<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Rejestracja</h1>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">Imię</label>
          <input
            v-model="form.firstName"
            type="text"
            id="firstName"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="Jan"
          />
        </div>

        <div>
          <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Nazwisko</label>
          <input
            v-model="form.lastName"
            type="text"
            id="lastName"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="Kowalski"
          />
        </div>

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
            placeholder="Minimum 8 znaków"
          />
        </div>

        <div>
          <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700 mb-2">Powtórz hasło</label>
          <input
            v-model="form.passwordConfirmation"
            type="password"
            id="passwordConfirmation"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
            placeholder="Powtórz hasło"
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
          {{ auth.isLoading ? 'Rejestracja...' : 'Zarejestruj się' }}
        </button>
      </form>

      <p class="text-center text-gray-600 mt-6">
        Masz już konto?
        <router-link to="/user/login" class="text-blue-600 hover:underline font-semibold">
          Zaloguj się
        </router-link>
      </p>
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
  firstName: '',
  lastName: '',
  email: '',
  password: '',
  passwordConfirmation: '',
});

const handleRegister = async () => {
  const success = await auth.register(
    form.firstName,
    form.lastName,
    form.email,
    form.password,
    form.passwordConfirmation
  );

  if (success) {
    router.push('/user/tasks');
  }
};
</script>
