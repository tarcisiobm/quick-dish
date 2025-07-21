import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from './error';
import { useSnackbarStore } from './snackbar';

export interface PaymentType {
  id: number;
  name: string;
  status: boolean;
}

export interface Coupon {
  id: number;
  code: string;
  description: string | null;
  discount_type: 'percentage' | 'fixed';
  discount_value: number | string;
  min_order_value: number | string;
  usage_limit: number | null;
  start_date: string;
  end_date: string;
  status: boolean;
}

export const usePaymentStore = defineStore('payment', () => {
  const errorStore = useErrorStore();
  const snackbarStore = useSnackbarStore();

  const paymentTypes = ref<PaymentType[]>([]);
  const coupons = ref<Coupon[]>([]);

  const paymentTypeList = computed(() => paymentTypes.value);
  const couponList = computed(() => coupons.value);

  async function fetchPaymentTypes() {
    try {
      const response = await api.get('/paymentTypes');
      paymentTypes.value = response.data.data;
    } catch (error) {
      errorStore.handle(error);
    }
  }

  async function fetchCoupons() {
    try {
      const response = await api.get('/coupons');
      coupons.value = response.data.data;
    } catch (error) {
      errorStore.handle(error);
    }
  }

  return {
    paymentTypes,
    coupons,
    paymentTypeList,
    couponList,
    fetchPaymentTypes,
    fetchCoupons,
  };
});
