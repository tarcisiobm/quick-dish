<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';

interface Table {
  id: number;
  number: number;
  capacity: number;
  status: boolean;
}

interface TableForm {
  number: number | null;
  capacity: number | null;
  status: boolean;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();

const tables = ref<Table[]>([]);
const loading = ref(true);
const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<Table | null>(null);
const itemToDelete = ref<Table | null>(null);

const form = ref<TableForm>({
  number: null,
  capacity: null,
  status: true
});

const formTitle = computed(() => (editedItem.value ? 'Editar Mesa' : 'Nova Mesa'));

const headers = [
  { title: 'Número da Mesa', key: 'number' },
  { title: 'Capacidade', key: 'capacity' },
  { title: 'Status', key: 'status', align: 'center' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

async function fetchTables() {
  loading.value = true;
  try {
    const response = await api.get('/tables');
    tables.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

function openNewDialog() {
  editedItem.value = null;
  form.value = { number: null, capacity: null, status: true };
  dialog.value = true;
}

function openEditDialog(item: Table) {
  editedItem.value = { ...item };
  form.value = {
    number: item.number,
    capacity: item.capacity,
    status: !!item.status
  };
  dialog.value = true;
}

function openDeleteDialog(item: Table) {
  itemToDelete.value = item;
  deleteDialog.value = true;
}

async function save() {
  try {
    let response;
    if (editedItem.value) {
      response = await api.put(`/tables/${editedItem.value.id}`, form.value);
    } else {
      response = await api.post('/tables', form.value);
    }
    snackbarStore.success(response.data.message);
    fetchTables();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function deleteItemConfirm() {
  if (!itemToDelete.value) return;
  try {
    const response = await api.delete(`/tables/${itemToDelete.value.id}`);
    snackbarStore.success(response.data.message);
    fetchTables();
    closeDeleteDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function toggleStatus(item: Table) {
  try {
    const payload = { ...item, status: !item.status };
    const response = await api.put(`/tables/${item.id}`, payload);
    snackbarStore.success(response.data.message);
    fetchTables();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
}

function closeDeleteDialog() {
  deleteDialog.value = false;
}

onMounted(fetchTables);
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Mesas do Estabelecimento</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Nova Mesa</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="tables" :loading="loading" class="text">
        <template v-slot:item.status="{ item }">
          <v-chip :color="item.status ? 'success' : 'grey'" size="small" @click="toggleStatus(item)" class="cursor-pointer">
            {{ item.status ? 'Disponível' : 'Indisponível' }}
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
          <v-text-field v-model="form.number" label="Número da Mesa" type="number"></v-text-field>
          <v-text-field v-model="form.capacity" label="Capacidade (Nº de Pessoas)" type="number"></v-text-field>
          <v-switch v-model="form.status" color="primary" label="Disponível para reservas"></v-switch>
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
        <v-card-text class="text">Tem certeza que deseja excluir a Mesa nº {{ itemToDelete?.number }}?</v-card-text>
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
