<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import { usePaymentStore, type Coupon } from '@/stores/payment';
import { useSnackbarStore } from '@/stores/snackbar';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

interface CouponForm {
  code: string;
  description: string | null;
  discount_type: 'percentage' | 'fixed';
  discount_value: number | null;
  min_order_value: number | null;
  usage_limit: number | null;
  start_date: string;
  end_date: string;
  status: boolean;
}

const paymentStore = usePaymentStore();
const snackbarStore = useSnackbarStore();
const errorStore = useErrorStore();

const dialog = ref(false);
const editedItem = ref<Coupon | null>(null);

const formDefault: CouponForm = {
  code: '',
  description: '',
  discount_type: 'fixed',
  discount_value: null,
  min_order_value: null,
  usage_limit: null,
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  status: true
};

const form = ref<CouponForm>({ ...formDefault });

const formTitle = computed(() => (editedItem.value ? 'Editar Cupom' : 'Novo Cupom'));

const headers = [
  { title: 'Código', key: 'code' },
  { title: 'Descrição', key: 'description' },
  { title: 'Desconto', key: 'discount_value' },
  { title: 'Status', key: 'status', align: 'center' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

function openNewDialog() {
  editedItem.value = null;
  form.value = { ...formDefault };
  dialog.value = true;
}

function openEditDialog(item: Coupon) {
  editedItem.value = { ...item };
  form.value = {
    code: item.code,
    description: item.description,
    discount_type: item.discount_type,
    discount_value: Number(item.discount_value),
    min_order_value: Number(item.min_order_value),
    usage_limit: item.usage_limit,
    start_date: item.start_date,
    end_date: item.end_date,
    status: !!item.status
  };
  dialog.value = true;
}

async function save() {
  try {
    let response;
    if (editedItem.value) {
      response = await api.put(`/coupons/${editedItem.value.id}`, form.value);
    } else {
      response = await api.post('/coupons', form.value);
    }
    snackbarStore.success(response.data.message);
    await paymentStore.fetchCoupons();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function toggleStatus(item: Coupon) {
  try {
    const payload = { ...item, status: !item.status };
    const response = await api.put(`/coupons/${item.id}`, payload);
    snackbarStore.success(response.data.message);
    await paymentStore.fetchCoupons();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
}

onMounted(() => {
  paymentStore.fetchCoupons();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Cupons de Desconto</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Cupom</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="paymentStore.couponList" class="text">
        <template v-slot:item.discount_value="{ item }">
          <span v-if="item.discount_type === 'fixed'">R$ {{ Number(item.discount_value).toFixed(2) }}</span>
          <span v-else>{{ Number(item.discount_value) }}%</span>
        </template>
        <template v-slot:item.status="{ item }">
          <v-chip :color="item.status ? 'success' : 'grey'" size="small" @click="toggleStatus(item)" class="cursor-pointer">
            {{ item.status ? 'Ativo' : 'Inativo' }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon icon="mdi-pencil" size="small" @click="openEditDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="700px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.code" label="Código do Cupom"></v-text-field></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="form.description" label="Descrição"></v-text-field></v-col>
          </v-row>
          <v-radio-group v-model="form.discount_type" inline label="Tipo de Desconto">
            <v-radio label="Valor Fixo (R$)" value="fixed" class="text"></v-radio>
            <v-radio label="Porcentagem (%)" value="percentage" class="text"></v-radio>
          </v-radio-group>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.discount_value" label="Valor do Desconto" type="number"></v-text-field></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="form.min_order_value" label="Valor Mínimo do Pedido" type="number" prefix="R$"></v-text-field></v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.start_date" label="Data de Início" type="date"></v-text-field></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="form.end_date" label="Data de Fim" type="date"></v-text-field></v-col>
          </v-row>
          <v-text-field v-model="form.usage_limit" label="Limite de Uso (deixe em branco para ilimitado)" type="number"></v-text-field>
          <v-switch v-model="form.status" color="primary" label="Ativo"></v-switch>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>
