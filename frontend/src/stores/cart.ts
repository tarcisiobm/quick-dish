import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from './error';
import { useSnackbarStore } from './snackbar';
import { useAuthStore } from './auth';
import router from '@/router';

export interface Product {
  id: number;
  name: string;
  price: string | number;
  image_path: string | null;
}

export interface CartItem {
  cartItemId: number;
  product: Product;
  quantity: number;
}

export const useCartStore = defineStore('cart', () => {
  const errorStore = useErrorStore();
  const snackbarStore = useSnackbarStore();
  const authStore = useAuthStore();

  const items = ref<CartItem[]>([]);
  const isDrawerOpen = ref(false);

  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0);
  });

  const subtotal = computed(() => {
    return items.value.reduce((total, item) => {
      return total + item.quantity * Number(item.product.price);
    }, 0);
  });

  const isEmpty = computed(() => items.value.length === 0);

  function addItem(productToAdd: Product) {
    const existingItem = items.value.find(item => item.product.id === productToAdd.id);
    if (existingItem) {
      existingItem.quantity++;
    } else {
      items.value.push({
        cartItemId: Date.now(),
        product: productToAdd,
        quantity: 1
      });
    }
    snackbarStore.success(`${productToAdd.name} adicionado ao carrinho!`);
  }

  function removeItem(cartItemIdToRemove: number) {
    items.value = items.value.filter(item => item.cartItemId !== cartItemIdToRemove);
  }

  function updateQuantity(cartItemIdToUpdate: number, newQuantity: number) {
    const item = items.value.find(item => item.cartItemId === cartItemIdToUpdate);
    if (item) {
      if (newQuantity > 0) {
        item.quantity = newQuantity;
      } else {
        removeItem(cartItemIdToUpdate);
      }
    }
  }

  function clearCart() {
    items.value = [];
  }

  function toggleDrawer() {
    isDrawerOpen.value = !isDrawerOpen.value;
  }

  async function submitOrder(orderDetails: { order_type: string; table_id?: number; delivery_id?: number; coupon_code?: string; notes?: string }) {
    if (isEmpty.value) {
      snackbarStore.error("Seu carrinho está vazio.");
      return;
    }

    if (!authStore.user) {
        snackbarStore.error("Você precisa estar logado para fazer um pedido.");
        router.push('/login');
        return;
    }

    const orderPayload = {
      ...orderDetails,
      user_id: authStore.user.id,
      items: items.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity,
        additionals: []
      }))
    };

    try {
      await api.post('/orders', orderPayload);
      snackbarStore.success("Pedido realizado com sucesso!");
      clearCart();
      isDrawerOpen.value = false;
      router.push('/');
    } catch (error) {
      errorStore.handle(error);
    }
  }

  return {
    items,
    isDrawerOpen,
    itemCount,
    subtotal,
    isEmpty,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    toggleDrawer,
    submitOrder
  };
});
