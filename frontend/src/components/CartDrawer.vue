<script setup lang="ts">
import { useCartStore } from '@/stores/cart';
import { useRouter } from 'vue-router';

const cartStore = useCartStore();
const router = useRouter();

function goToCheckout() {
  cartStore.toggleDrawer();
  router.push('/checkout');
}
</script>

<template>
  <v-navigation-drawer v-model="cartStore.isDrawerOpen" temporary location="right" width="400" style="position: fixed; height: 100vh;">
    <div class="d-flex flex-column fill-height">
      <v-toolbar color="background">
        <v-toolbar-title class="subtitle">Seu Carrinho</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" @click="cartStore.toggleDrawer"></v-btn>
      </v-toolbar>

      <v-divider></v-divider>

      <div v-if="cartStore.isEmpty" class="d-flex flex-column align-center justify-center flex-grow-1 text-center">
        <v-icon icon="mdi-cart-off" size="64" color="grey"></v-icon>
        <p class="text color-subtitle mt-4">Seu carrinho está vazio.</p>
      </div>

      <v-list v-else class="flex-grow-1" style="overflow-y: auto">
        <v-list-item v-for="item in cartStore.items" :key="item.cartItemId" lines="two">
          <template #prepend>
            <v-avatar rounded="lg">
              <v-img :src="item.product.image_path || undefined">
                <template #error><v-icon icon="mdi-food-off-outline"></v-icon></template>
              </v-img>
            </v-avatar>
          </template>

          <v-list-item-title class="subtitle">{{ item.product.name }}</v-list-item-title>
          <v-list-item-subtitle class="text">R$ {{ Number(item.product.price).toFixed(2) }}</v-list-item-subtitle>

          <template #append>
            <div class="d-flex align-center ga-2">
              <v-btn-toggle variant="outlined" density="compact" divided>
                <v-btn size="x-small" @click="cartStore.updateQuantity(item.cartItemId, item.quantity - 1)">-</v-btn>
                <v-btn size="x-small" disabled>{{ item.quantity }}</v-btn>
                <v-btn size="x-small" @click="cartStore.updateQuantity(item.cartItemId, item.quantity + 1)">+</v-btn>
              </v-btn-toggle>
              <v-btn icon="mdi-trash-can-outline" variant="text" size="small" color="error" @click="cartStore.removeItem(item.cartItemId)"></v-btn>
            </div>
          </template>
        </v-list-item>
      </v-list>

      <template v-if="!cartStore.isEmpty">
        <v-divider></v-divider>
        <div class="pa-4">
          <div class="d-flex justify-space-between mb-4">
            <span class="subtitle">Subtotal</span>
            <span class="title">R$ {{ cartStore.subtotal.toFixed(2) }}</span>
          </div>
          <v-btn block @click="goToCheckout">Finalizar Pedido</v-btn>
        </div>
      </template>
    </div>
  </v-navigation-drawer>
</template>
