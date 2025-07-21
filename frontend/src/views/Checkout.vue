<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCartStore } from '@/stores/cart';
import { useRouter } from 'vue-router';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

interface Table {
  id: number;
  number: number;
  capacity: number;
}
interface Address {
  id: number;
  street: string;
  number: string;
  city: string;
}

const cartStore = useCartStore();
const errorStore = useErrorStore();
const router = useRouter();

const orderType = ref('takeout');
const tables = ref<Table[]>([]);
const addresses = ref<Address[]>([]);

const selectedTableId = ref<number | null>(null);
const selectedAddressId = ref<number | null>(null);
const couponCode = ref('');
const notes = ref('');

if (cartStore.isEmpty) {
  router.push('/menu');
}

async function fetchTables() {
  try {
    const response = await api.get('/tables');
    tables.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  }
}

async function fetchAddresses() {
  try {
    const response = await api.get('/addresses');
  } catch (error) {
    errorStore.handle(error);
  }
}

function finalizeOrder() {
  if (orderType.value === 'dine-in' && !selectedTableId.value) {
    errorStore.handle({ response: { data: { message: 'Por favor, selecione uma mesa.' } } });
    return;
  }
  if (orderType.value === 'delivery' && !selectedAddressId.value) {
    errorStore.handle({ response: { data: { message: 'Por favor, selecione um endereço.' } } });
    return;
  }

  cartStore.submitOrder({
    order_type: orderType.value,
    table_id: selectedTableId.value ?? undefined,
    delivery_id: selectedAddressId.value ? 1 : undefined, // Assumindo que o delivery_id=1 por enquanto
    coupon_code: couponCode.value,
    notes: notes.value
  });
}

onMounted(() => {
  fetchTables();
  fetchAddresses();
});
</script>

<template>
  <v-container>
    <h1 class="title text-h4 mt-4 mb-8">Finalizar Pedido</h1>
    <v-row>
      <v-col cols="12" md="7">
        <v-card variant="outlined" class="mb-4">
          <v-card-title class="subtitle">Resumo do Pedido</v-card-title>
          <v-list>
            <v-list-item v-for="item in cartStore.items" :key="item.cartItemId">
              <v-list-item-title class="text">{{ item.product.name }} ({{ item.quantity }}x)</v-list-item-title>
              <template #append>
                <span class="text">R$ {{ (Number(item.product.price) * item.quantity).toFixed(2) }}</span>
              </template>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-card-text class="d-flex justify-space-between">
            <span class="subtitle">Subtotal</span>
            <span class="subtitle">R$ {{ cartStore.subtotal.toFixed(2) }}</span>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="5">
        <v-card variant="outlined">
          <v-card-title class="subtitle">Detalhes da Entrega</v-card-title>
          <v-card-text>
            <v-radio-group v-model="orderType" inline label="Tipo de Pedido" class="mb-4">
              <v-radio label="Retirada" value="takeout" class="text"></v-radio>
              <v-radio label="Consumo Local" value="dine-in" class="text"></v-radio>
              <v-radio label="Entrega" value="delivery" class="text"></v-radio>
            </v-radio-group>

            <v-select v-if="orderType === 'dine-in'" v-model="selectedTableId" :items="tables" item-title="number" item-value="id" label="Selecione a Mesa">
              <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props" :title="`Mesa ${item.raw.number}`" :subtitle="`Capacidade: ${item.raw.capacity}`"></v-list-item>
              </template>
            </v-select>

            <v-select v-if="orderType === 'delivery'" v-model="selectedAddressId" :items="addresses" item-title="street" item-value="id" label="Selecione o Endereço">
              <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props" :title="`${item.raw.street}, ${item.raw.number}`" :subtitle="item.raw.city"></v-list-item>
              </template>
            </v-select>

            <v-text-field v-model="couponCode" label="Código do Cupom" placeholder="Ex: PROMO10" class="mt-4" />
            <v-textarea v-model="notes" label="Observações (opcional)" />
          </v-card-text>
        </v-card>
        <v-btn @click="finalizeOrder" block class="mt-4" size="large">Confirmar Pedido</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>
