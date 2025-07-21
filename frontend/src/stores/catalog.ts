import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from './error';
import { useSnackbarStore } from './snackbar';

export interface Category {
  id: number;
  name: string;
  description?: string;
  display_order: number;
  image_path?: string;
}

export const useCatalogStore = defineStore('catalog', () => {
  const errorStore = useErrorStore();
  const snackbarStore = useSnackbarStore();

  const categories = ref<Category[]>([]);
  const categoryList = computed(() => categories.value);

  async function fetchCategories() {
    try {
      const response = await api.get('/categories');
      categories.value = response.data.data;
    } catch (error) {
      errorStore.handle(error);
    }
  }

  async function createCategory(data: Omit<Category, 'id'>): Promise<Category | null> {
    try {
      const response = await api.post('/categories', data);
      snackbarStore.success(response.data.message);
      await fetchCategories();
      return response.data.data;
    } catch (error) {
      errorStore.handle(error);
      return null;
    }
  }

  return {
    categories,
    categoryList,
    fetchCategories,
    createCategory
  };
});
