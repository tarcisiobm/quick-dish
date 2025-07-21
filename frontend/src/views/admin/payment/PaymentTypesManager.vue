<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import { usePaymentStore, type PaymentType } from '@/stores/payment';
import { useSnackbarStore } from '@/stores/snackbar';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

const paymentStore = usePaymentStore();
const snackbarStore = useSnackbarStore();
const errorStore = useErrorStore();

const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<PaymentType | null>(null);
const itemToDelete = ref<PaymentType | null>(null);
const form = ref({ name: '', status: true });

const formTitle = computed(() => (editedItem.value ? 'Editar Tipo de Pagamento' : 'Novo Tipo de Pagamento'));

const headers = [
  { title: 'Nome', key: 'name' },
  { title: 'Status', key: 'status', align: 'center' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

function openNewDialog() {
  editedItem.value = null;
  form.value = { name: '', status: true };
  dialog.value = true;
}

function openEditDialog(item: PaymentType) {
  editedItem.value = { ...item };
  form.value = {
    name: item.name,
    status: !!item.status
  };
  dialog.value = true;
}

function openDeleteDialog(item: PaymentType) {
  itemToDelete.value = item;
  deleteDialog.value = true;
}

async function save() {
  try {
    let response;
    if (editedItem.value) {
      response = await api.put(`/paymentTypes/${editedItem.value.id}`, form.value);
    } else {
      response = await api.post('/paymentTypes', form.value);
    }
    snackbarStore.success(response.data.message);
    await paymentStore.fetchPaymentTypes();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function deleteItemConfirm() {
  if (!itemToDelete.value) return;
  try {
    const response = await api.delete(`/paymentTypes/${itemToDelete.value.id}`);
    snackbarStore.success(response.data.message);
    await paymentStore.fetchPaymentTypes();
    closeDeleteDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function toggleStatus(item: PaymentType) {
  try {
    const payload = { ...item, status: !item.status };
    const response = await api.put(`/paymentTypes/${item.id}`, payload);
    snackbarStore.success(response.data.message);
    await paymentStore.fetchPaymentTypes();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
  nextTick(() => {
    editedItem.value = null;
    form.value = { name: '', status: true };
  });
}

function closeDeleteDialog() {
  deleteDialog.value = false;
  nextTick(() => {
    itemToDelete.value = null;
  });
}

onMounted(() => {
  paymentStore.fetchPaymentTypes();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Tipos de Pagamento</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Tipo</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="paymentStore.paymentTypeList" class="text">
        <template v-slot:item.status="{ item }">
          <v-chip :color="item.status ? 'success' : 'grey'" size="small" @click="toggleStatus(item)" class="cursor-pointer">
            {{ item.status ? 'Ativo' : 'Inativo' }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon icon="mdi-pencil" size="small" class="mr-2" @click="openEditDialog(item)"></v-icon>
          <v-icon icon="mdi-delete" size="small" @click="openDeleteDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="500px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.name" label="Nome (ex: Cartão de Crédito)"></v-text-field>
          <v-switch v-model="form.status" color="primary" label="Ativo"></v-switch>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5 subtitle">Confirmar Exclusão</v-card-title>
        <v-card-text class="text">Tem certeza que deseja excluir o tipo de pagamento "{{ itemToDelete?.name }}"?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="text" variant="text" @click="closeDeleteDialog">Cancelar</v-btn>
          <v-btn color="error" variant="elevated" @click="deleteItemConfirm">Excluir</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>
