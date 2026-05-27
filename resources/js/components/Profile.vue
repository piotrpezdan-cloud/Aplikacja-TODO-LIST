<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Mój profil</h1>

    <form @submit.prevent="save" class="bg-white rounded-lg shadow p-6 space-y-5">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="label" for="firstName">Imię</label>
          <input id="firstName" v-model="form.first_name" class="input" required />
        </div>
        <div>
          <label class="label" for="lastName">Nazwisko</label>
          <input id="lastName" v-model="form.last_name" class="input" required />
        </div>
      </div>

      <div>
        <label class="label" for="email">Email</label>
        <input id="email" v-model="form.email" type="email" class="input" required />
      </div>

      <div>
        <label class="label">Rola</label>
        <p class="text-gray-700">{{ auth.user.role === 'admin' ? 'Administrator' : 'Użytkownik' }}</p>
      </div>

      <div v-if="auth.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ auth.error }}</div>
      <div v-if="saved" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">Profil zapisany.</div>

      <button :disabled="auth.isLoading" class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded-lg">
        {{ auth.isLoading ? 'Zapisywanie...' : 'Zapisz profil' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '../stores/authStore';

const auth = useAuthStore();
const saved = ref(false);

const form = reactive({
  first_name: auth.user?.first_name || '',
  last_name: auth.user?.last_name || '',
  email: auth.user?.email || '',
});

const save = async () => {
  saved.value = false;
  saved.value = await auth.updateProfile(form);
};
</script>

<style scoped>
.label { display: block; margin-bottom: 0.4rem; font-size: 0.875rem; font-weight: 600; color: #374151; }
.input { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 0.55rem 0.75rem; outline: none; }
.input:focus { border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15); }
</style>
