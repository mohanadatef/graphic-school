import { reactive, computed } from 'vue';
import { useApi } from './useApi';

const savedUser = localStorage.getItem('gs_user');
const savedToken = localStorage.getItem('gs_token');

const state = reactive({
  user: savedUser ? JSON.parse(savedUser) : null,
  token: savedToken || null,
  loading: false,
  error: null,
});

export function useAuth() {
  const { post, error: apiError } = useApi();

  async function login(credentials) {
    state.loading = true;
    state.error = null;
    try {
      const data = await post('/login', credentials);
      if (data && data.user && data.token) {
        setSession(data.user, data.token);
        return data.user;
      } else {
        throw new Error('Invalid response from server');
      }
    } catch (error) {
      state.error = apiError.value || error.response?.data?.message || 'حدث خطأ أثناء تسجيل الدخول';
      throw error;
    } finally {
      state.loading = false;
    }
  }

  async function register(payload) {
    state.loading = true;
    state.error = null;
    try {
      const data = await post('/register', payload);
      if (data && data.user && data.token) {
        setSession(data.user, data.token);
        return data.user;
      } else {
        throw new Error('Invalid response from server');
      }
    } catch (error) {
      state.error = apiError.value || error.response?.data?.message || 'حدث خطأ أثناء التسجيل';
      throw error;
    } finally {
      state.loading = false;
    }
  }

  function setSession(user, token) {
    state.user = user;
    state.token = token;
    localStorage.setItem('gs_user', JSON.stringify(user));
    localStorage.setItem('gs_token', token);
  }

  async function logout() {
    try {
      await post('/logout');
    } catch (error) {
      // ignore logout errors
    } finally {
      state.user = null;
      state.token = null;
      localStorage.removeItem('gs_user');
      localStorage.removeItem('gs_token');
    }
  }

  const roleName = computed(() => state.user?.role_name || state.user?.role?.name);
  const isAdmin = computed(() => roleName.value === 'admin');
  const isInstructor = computed(() => roleName.value === 'instructor');
  const isStudent = computed(() => roleName.value === 'student');

  return {
    state,
    login,
    register,
    logout,
    setSession,
    isAdmin,
    isInstructor,
    isStudent,
    roleName,
  };
}

