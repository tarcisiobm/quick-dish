<script setup lang="ts">
import { ref } from 'vue';
import PaymentTypesManager from '@/views/admin/payment/PaymentTypesManager.vue';
import CouponsManager from '@/views/admin/payment/CouponsManager.vue';

const tab = ref('payment-types');
</script>
<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gestão de Pagamentos e Marketing</h1>
    <p class="text text-body-1 color-subtitle mb-6">Defina os métodos de pagamento aceitos e gerencie os cupons de desconto.</p>

    <v-tabs v-model="tab" color="primary" grow>
      <v-tab value="payment-types" class="text">Tipos de Pagamento</v-tab>
      <v-tab value="coupons" class="text">Cupons</v-tab>
    </v-tabs>

    <v-window v-model="tab" class="mt-5">
      <v-window-item value="payment-types">
        <PaymentTypesManager />
      </v-window-item>
      <v-window-item value="coupons">
        <CouponsManager />
      </v-window-item>
    </v-window>
  </v-container>
</template>
