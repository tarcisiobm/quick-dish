<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import { useInventoryStore, type Supplier } from '@/stores/inventory';
import { useSnackbarStore } from '@/stores/snackbar';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

const inventoryStore = useInventoryStore();
const snackbarStore = useSnackbarStore();
const errorStore = useErrorStore();

const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<Supplier | null>(null);
const itemToDelete = ref<Supplier | null>(null);
const form = ref<Omit<Supplier, 'id'>>({ name: '', cnpj: '', phone: '', email: '' });

const formTitle = computed(() => (editedItem.value ? 'Editar Fornecedor' : 'Novo Fornecedor'));

const headers = [
  { title: 'Nome', key: 'name' }, { title: 'CNPJ', key: 'cnpj' },
  { title: 'Telefone', key: 'phone' }, { title: 'Email', key: 'email' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' },
] as const;

function openNewDialog() {
  editedItem.value = null;
  form.value = { name: '', cnpj: '', phone: '', email: '' };
  dialog.value = true;
}

function openEditDialog(item: Supplier) {
  editedItem.value = { ...item };
  form.value = { ...item };
  dialog.value = true;
}

function openDeleteDialog(item: Supplier) {
  itemToDelete.value = item;
  deleteDialog.value = true;
}

async function save() {
  try {
    if (editedItem.value) {
      const response = await api.put(`/suppliers/${editedItem.value.id}`, form.value);
      snackbarStore.success(response.data.message);
    } else {
      await inventoryStore.createSupplier(form.value);
    }
    await inventoryStore.fetchSuppliers();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function deleteItemConfirm() {
  if (!itemToDelete.value) return;
  try {
    const response = await api.delete(`/suppliers/${itemToDelete.value.id}`);
    snackbarStore.success(response.data.message);
    await inventoryStore.fetchSuppliers();
    closeDeleteDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
  nextTick(() => {
    editedItem.value = null;
    form.value = { name: '', cnpj: '', phone: '', email: '' };
  });
}

function closeDeleteDialog() {
  deleteDialog.value = false;
  nextTick(() => {
    itemToDelete.value = null;
  });
}

onMounted(() => {
  inventoryStore.fetchSuppliers();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Fornecedores</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Fornecedor</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="inventoryStore.supplierList" class="text">
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
          <v-text-field v-model="form.name" label="Nome"></v-text-field>
          <v-text-field v-model="form.cnpj" label="CNPJ"></v-text-field>
          <v-text-field v-model="form.phone" label="Telefone"></v-text-field>
          <v-text-field v-model="form.email" label="Email"></v-text-field>
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
        <v-card-text class="text">Tem certeza que deseja excluir o fornecedor "{{ itemToDelete?.name }}"?</v-card-text>
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
