import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from './error';
import { useSnackbarStore } from './snackbar';

// Interfaces
export interface UnitMeasure {
  id: number;
  name: string;
  abbreviation: string;
}

export interface Supplier {
  id: number;
  name: string;
  cnpj?: string;
  phone?: string;
  email?: string;
}

export const useInventoryStore = defineStore('inventory', () => {
  const errorStore = useErrorStore();
  const snackbarStore = useSnackbarStore();

  const suppliers = ref<Supplier[]>([]);
  const unitMeasures = ref<UnitMeasure[]>([]);

  const supplierList = computed(() => suppliers.value);
  const unitMeasureList = computed(() => unitMeasures.value);

  async function fetchSuppliers() {
    try {
      const response = await api.get('/suppliers');
      suppliers.value = response.data.data;
    } catch (error) {
      errorStore.handle(error);
    }
  }

  async function fetchUnitMeasures() {
    try {
      const response = await api.get('/unitMeasures');
      unitMeasures.value = response.data.data;
    } catch (error) {
      errorStore.handle(error);
    }
  }

  async function createSupplier(data: Omit<Supplier, 'id'>): Promise<Supplier | null> {
    try {
      const response = await api.post('/suppliers', data);
      snackbarStore.success(response.data.message);
      await fetchSuppliers()
      return response.data.data;
    } catch (error) {
      errorStore.handle(error);
      return null;
    }
  }

  async function createUnitMeasure(data: Omit<UnitMeasure, 'id'>): Promise<UnitMeasure | null> {
    try {
      const response = await api.post('/unitMeasures', data);
      snackbarStore.success(response.data.message);
      await fetchUnitMeasures();
      return response.data.data;
    } catch (error) {
      errorStore.handle(error);
      return null;
    }
  }

  return {
    suppliers,
    unitMeasures,
    supplierList,
    unitMeasureList,
    fetchSuppliers,
    fetchUnitMeasures,
    createSupplier,
    createUnitMeasure
  };
});
