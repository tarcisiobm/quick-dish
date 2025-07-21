<script setup lang="ts">
import { ref } from 'vue';
import UserOrders from '@/views/account/UserOrders.vue';
import UserReservations from '@/views/account/UserReservations.vue';

const tab = ref('orders');
</script>

<template>
  <v-container>
    <h1 class="title text-h4 mt-4 mb-8">Minha Conta</h1>
    <v-tabs v-model="tab" color="primary" class="mb-6">
      <v-tab value="orders" class="text">Meus Pedidos</v-tab>
      <v-tab value="reservations" class="text">Minhas Reservas</v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <v-window-item value="orders">
        <UserOrders />
      </v-window-item>
      <v-window-item value="reservations">
        <UserReservations />
      </v-window-item>
    </v-window>
  </v-container>
</template>
