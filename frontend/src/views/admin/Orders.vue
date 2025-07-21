<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';

interface Ingredient {
  name: string;
}

interface Additional {
  quantity: number;
  unit_price: string;
  ingredient: Ingredient;
}

interface OrderItem {
  quantity: number;
  unit_price: string;
  notes: string | null;
  product: { name: string };
  additionals: Additional[];
}

interface Order {
  id: number;
  status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'delivered' | 'cancelled';
  total: string;
  created_at: string;
  notes: string | null;
  user: { name: string };
  table: { number: number } | null;
  order_type: string;
  items: OrderItem[];
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();

const orders = ref<Order[]>([]);
const loading = ref(true);
const search = ref('');
const debounce = ref<number | null>(null);

const detailsDialog = ref(false);
const loadingDetails = ref(false);
const selectedOrder = ref<Order | null>(null);

const headers = [
  { title: 'Pedido ID', key: 'id', width: '120px' },
  { title: 'Cliente', key: 'user.name' },
  { title: 'Data', key: 'created_at' },
  { title: 'Mesa/Tipo', key: 'table' },
  { title: 'Total', key: 'total' },
  { title: 'Status', key: 'status', align: 'center' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

const statusOptions = [
  { title: 'Pendente', value: 'pending', color: 'grey' },
  { title: 'Confirmado', value: 'confirmed', color: 'blue' },
  { title: 'Em Preparo', value: 'preparing', color: 'orange' },
  { title: 'Pronto', value: 'ready', color: 'amber' },
  { title: 'Entregue', value: 'delivered', color: 'success' },
  { title: 'Cancelado', value: 'cancelled', color: 'error' }
];

const statusMap = computed(() => {
  const map = new Map<string, { text: string; color: string }>();
  statusOptions.forEach((opt) => map.set(opt.value, { text: opt.title, color: opt.color }));
  return map;
});

async function fetchOrders() {
  loading.value = true;
  try {
    const params = { search: search.value };
    const response = await api.get('/orders', { params });
    orders.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

async function openDetailsDialog(order: Order) {
  loadingDetails.value = true;
  detailsDialog.value = true;
  try {
    const response = await api.get(`/orders/${order.id}`);
    selectedOrder.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
    detailsDialog.value = false;
  } finally {
    loadingDetails.value = false;
  }
}

async function updateStatus(order: Order, newStatus: string) {
  try {
    const response = await api.put(`/orders/${order.id}`, { status: newStatus });
    snackbarStore.success(response.data.message || 'Status atualizado!');
    fetchOrders();
  } catch (error) {
    errorStore.handle(error);
  }
}

function formatDateTime(dateTimeString: string) {
  return new Date(dateTimeString).toLocaleString('pt-BR');
}

watch(search, () => {
  if (debounce.value) clearTimeout(debounce.value);
  debounce.value = window.setTimeout(() => {
    fetchOrders();
  }, 500);
});

onMounted(fetchOrders);
</script>

<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gerenciamento de Pedidos</h1>
    <p class="text text-body-1 color-subtitle mb-6">Acompanhe e atualize o status de todos os pedidos recebidos.</p>

    <v-card flat>
      <v-card-title>
        <v-text-field v-model="search" append-inner-icon="mdi-magnify" label="Pesquisar por ID do Pedido ou Nome do Cliente..." single-line hide-details variant="outlined" density="compact" clearable></v-text-field>
      </v-card-title>
      <v-card-text>
        <v-data-table :headers="headers" :items="orders" :loading="loading" class="text">
          <template v-slot:item.created_at="{ value }">
            {{ formatDateTime(value) }}
          </template>

          <template v-slot:item.table="{ item }">
            <span v-if="item.table">Mesa {{ item.table.number }}</span>
            <span v-else class="text-capitalize">{{ item.order_type }}</span>
          </template>

          <template v-slot:item.total="{ value }">
            <span class="subtitle">R$ {{ Number(value).toFixed(2) }}</span>
          </template>

          <template v-slot:item.status="{ item }">
            <v-menu>
              <template v-slot:activator="{ props }">
                <v-chip :color="statusMap.get(item.status)?.color" variant="tonal" v-bind="props" class="cursor-pointer">
                  {{ statusMap.get(item.status)?.text }}
                </v-chip>
              </template>
              <v-list density="compact">
                <v-list-item v-for="status in statusOptions" :key="status.value" @click="updateStatus(item, status.value)" :disabled="item.status === status.value">
                  <v-list-item-title>{{ status.title }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>

          <template v-slot:item.actions="{ item }">
            <v-btn icon="mdi-eye" variant="text" size="small" @click="openDetailsDialog(item)"></v-btn>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>

    <v-dialog v-model="detailsDialog" max-width="500px">
      <v-card>
        <div v-if="loadingDetails" class="d-flex justify-center align-center" style="height: 400px">
          <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        </div>
        <div v-else-if="selectedOrder">
          <v-card-title class="d-flex justify-space-between align-center">
            <span class="subtitle text-h6">Pedido #{{ selectedOrder.id }}</span>
            <v-chip :color="statusMap.get(selectedOrder.status)?.color" variant="tonal">
              {{ statusMap.get(selectedOrder.status)?.text }}
            </v-chip>
          </v-card-title>
          <v-card-subtitle class="d-flex ga-4 text">
            <span><v-icon icon="mdi-account" size="small" class="mr-1"></v-icon>{{ selectedOrder.user.name }}</span>
            <span v-if="selectedOrder.table"><v-icon icon="mdi-table-chair" size="small" class="mr-1"></v-icon>Mesa {{ selectedOrder.table.number }}</span>
            <span v-else class="text-capitalize"><v-icon icon="mdi-package-variant-closed" size="small" class="mr-1"></v-icon>{{ selectedOrder.order_type }}</span>
          </v-card-subtitle>

          <v-divider class="my-3"></v-divider>

          <v-card-text style="max-height: 400px; overflow-y: auto">
            <p class="subtitle mb-2">Itens para Preparo</p>
            <v-list lines="three">
              <div v-for="item in selectedOrder.items" :key="item.product.name">
                <v-list-item class="px-0">
                  <v-list-item-title class="subtitle d-flex">
                    <span class="mr-2">{{ item.quantity }}x</span>
                    <span>{{ item.product.name }}</span>
                  </v-list-item-title>
                  <v-list-item-subtitle v-if="item.additionals && item.additionals.length > 0" class="pl-6">
                    <div v-for="additional in item.additionals" :key="additional.ingredient.name">+ {{ additional.quantity }}x {{ additional.ingredient.name }}</div>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle v-if="item.notes" class="pl-6 font-italic"> Obs: "{{ item.notes }}" </v-list-item-subtitle>
                </v-list-item>
                <v-divider></v-divider>
              </div>
            </v-list>

            <div v-if="selectedOrder.notes" class="mt-4">
              <p class="subtitle">Observações Gerais do Pedido:</p>
              <p class="text">{{ selectedOrder.notes }}</p>
            </div>
          </v-card-text>

          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="text" variant="text" @click="detailsDialog = false">Fechar</v-btn>
          </v-card-actions>
        </div>
      </v-card>
    </v-dialog>
  </v-container>
</template>
