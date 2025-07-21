<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';

interface OrderItem {
  quantity: number;
  unit_price: string;
  product: {
    name: string;
  };
}

interface Order {
  id: number;
  status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'delivered' | 'cancelled';
  total: string;
  created_at: string;
  items: OrderItem[];
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const orders = ref<Order[]>([]);
const loading = ref(true);
const cancelDialog = ref(false);
const itemToCancel = ref<Order | null>(null);

async function fetchMyOrders() {
  loading.value = true;
  try {
    const response = await api.get('/user/orders');
    orders.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

const statusMap = computed(() => ({
  pending: { text: 'Pendente', color: 'grey' },
  confirmed: { text: 'Confirmado', color: 'blue' },
  preparing: { text: 'Em Preparo', color: 'orange' },
  ready: { text: 'Pronto', color: 'amber' },
  delivered: { text: 'Entregue', color: 'success' },
  cancelled: { text: 'Cancelado', color: 'error' }
}));

function openCancelDialog(order: Order) {
  itemToCancel.value = order;
  cancelDialog.value = true;
}

function closeCancelDialog() {
  cancelDialog.value = false;
  nextTick(() => {
    itemToCancel.value = null;
  });
}

async function confirmCancelOrder() {
  if (!itemToCancel.value) return;
  try {
    const response = await api.post(`/orders/${itemToCancel.value.id}/cancel`);
    snackbarStore.success(response.data.message || 'Pedido cancelado com sucesso!');
    fetchMyOrders();
    closeCancelDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

function formatDateTime(dateTimeString: string) {
  return new Date(dateTimeString).toLocaleString('pt-BR');
}

onMounted(fetchMyOrders);
</script>

<template>
  <div v-if="loading" class="text-center pa-16">
    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
  </div>

  <v-expansion-panels v-else-if="orders.length > 0" variant="accordion">
    <v-expansion-panel v-for="order in orders" :key="order.id">
      <v-expansion-panel-title>
        <v-row no-gutters class="d-flex align-center">
          <v-col cols="4" class="text">Pedido #{{ order.id }}</v-col>
          <v-col cols="4" class="d-flex justify-center">
            <v-chip :color="statusMap[order.status]?.color" size="small" variant="tonal">{{ statusMap[order.status]?.text }}</v-chip>
          </v-col>
          <v-col cols="4" class="text-right subtitle">R$ {{ Number(order.total).toFixed(2) }}</v-col>
        </v-row>
      </v-expansion-panel-title>

      <v-expansion-panel-text>
        <v-list-item-subtitle class="text mb-2">Realizado em: {{ formatDateTime(order.created_at) }}</v-list-item-subtitle>
        <v-divider></v-divider>
        <v-list density="compact" class="mt-2">
          <v-list-item v-for="(item, index) in order.items" :key="index">
            <v-list-item-title class="text">{{ item.product.name }}</v-list-item-title>
            <v-list-item-subtitle class="text">{{ item.quantity }}x R$ {{ Number(item.unit_price).toFixed(2) }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>
        <v-card-actions v-if="['pending', 'preparing'].includes(order.status)">
          <v-spacer></v-spacer>
          <v-btn color="error" variant="text" @click="openCancelDialog(order)">Cancelar Pedido</v-btn>
        </v-card-actions>
      </v-expansion-panel-text>
    </v-expansion-panel>
  </v-expansion-panels>

  <div v-else class="text-center py-16">
    <v-icon icon="mdi-receipt-text-outline" size="64" color="grey"></v-icon>
    <p class="text text-h6 color-subtitle mt-4">Você ainda não fez nenhum pedido.</p>
  </div>

  <v-dialog v-model="cancelDialog" max-width="450px" persistent>
    <v-card>
      <v-card-text class="text-center pa-8">
        <v-icon icon="mdi-alert-circle-outline" color="alert" size="64" class="mb-4"></v-icon>
        <h2 class="subtitle text-h5 mb-2">Cancelar Pedido</h2>
        <p class="text color-subtitle">
          Tem certeza que deseja cancelar o pedido <strong>#{{ itemToCancel?.id }}</strong
          >?<br />Esta ação não pode ser desfeita.
        </p>
      </v-card-text>
      <v-card-actions class="pb-4">
        <v-spacer></v-spacer>
        <v-btn color="text" variant="text" @click="closeCancelDialog">Manter Pedido</v-btn>
        <v-btn color="error" variant="elevated" @click="confirmCancelOrder">Sim, Cancelar</v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
