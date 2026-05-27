import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
  const token = ref(localStorage.getItem('token'));
  const isLoading = ref(false);
  const error = ref(null);

  const login = async (email, password) => {
    isLoading.value = true;
    error.value = null;
    try {
      const { data } = await api.post('/login', {
        email,
        password,
        device_name: 'web-app',
      });

      token.value = data.token;
      user.value = data.user;

      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd logowania';
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const register = async (firstName, lastName, email, password, passwordConfirmation) => {
    isLoading.value = true;
    error.value = null;
    try {
      const { data } = await api.post('/register', {
        first_name: firstName,
        last_name: lastName,
        email,
        password,
        password_confirmation: passwordConfirmation,
        device_name: 'web-app',
      });

      token.value = data.token;
      user.value = data.user;

      localStorage.setItem('token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd rejestracji';
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    isLoading.value = true;
    try {
      await api.post('/logout');
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      isLoading.value = false;
    }
  };

  const updateProfile = async (profileData) => {
    isLoading.value = true;
    error.value = null;
    try {
      const { data } = await api.put('/profile', profileData);
      user.value = data.data;
      localStorage.setItem('user', JSON.stringify(data.data));
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Błąd aktualizacji profilu';
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const isAuthenticated = () => !!token.value && !!user.value;

  return {
    user,
    token,
    isLoading,
    error,
    login,
    register,
    logout,
    updateProfile,
    isAuthenticated,
  };
});
