<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';

interface Reservation {
  id: number;
  user: { name: string };
  table: { number: number };
  guests_count: number;
  reservation_date: string;
  start_time: string;
  end_time: string;
  status: 'confirmed' | 'cancelled';
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();

const reservations = ref<Reservation[]>([]);
const loading = ref(true);
const dialog = ref(false);
const cancelDialog = ref(false);
const editedItem = ref<Reservation | null>(null);
const itemToCancel = ref<Reservation | null>(null);

const form = ref({
  reservation_date: '',
  start_time: '',
  end_time: ''
});

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Cliente', key: 'user.name' },
  { title: 'Mesa', key: 'table.number' },
  { title: 'Data', key: 'reservation_date' },
  { title: 'Horário', key: 'start_time' },
  { title: 'Status', key: 'status' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

async function fetchReservations() {
  loading.value = true;
  try {
    const response = await api.get('/reservations');
    reservations.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

function openEditDialog(item: Reservation) {
  editedItem.value = { ...item };
  form.value = {
    reservation_date: item.reservation_date,
    start_time: item.start_time,
    end_time: item.end_time
  };
  dialog.value = true;
}

function openCancelDialog(item: Reservation) {
  itemToCancel.value = item;
  cancelDialog.value = true;
}

async function save() {
  if (!editedItem.value) return;
  try {
    const response = await api.put(`/reservations/${editedItem.value.id}`, form.value);
    snackbarStore.success(response.data.message);
    fetchReservations();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function confirmCancel() {
  if (!itemToCancel.value) return;
  try {
    const payload = { status: 'cancelled' };
    const response = await api.put(`/reservations/${itemToCancel.value.id}`, payload);
    snackbarStore.success(response.data.message);
    fetchReservations();
    closeCancelDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
}
function closeCancelDialog() {
  cancelDialog.value = false;
}

onMounted(fetchReservations);
</script>

<template>
  <v-card flat>
    <v-card-title class="subtitle">Gerenciamento de Reservas</v-card-title>
    <v-card-text>
      <v-data-table :headers="headers" :items="reservations" :loading="loading" class="text">
        <template v-slot:item.start_time="{ item }"> {{ item.start_time }} - {{ item.end_time }} </template>
        <template v-slot:item.status="{ item }">
          <v-chip :color="item.status === 'confirmed' ? 'success' : 'error'" size="small">
            {{ item.status === 'confirmed' ? 'Confirmada' : 'Cancelada' }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon icon="mdi-pencil" size="small" class="mr-2" @click="openEditDialog(item)"></v-icon>
          <v-icon v-if="item.status === 'confirmed'" icon="mdi-close-circle" size="small" color="error" @click="openCancelDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="500px" persistent>
      <v-card>
        <v-card-title class="subtitle">Editar Reserva</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.reservation_date" label="Data da Reserva" type="date"></v-text-field>
          <v-text-field v-model="form.start_time" label="Horário de Chegada" type="time"></v-text-field>
          <v-text-field v-model="form.end_time" label="Horário de Saída" type="time"></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar Alterações</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="cancelDialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5 subtitle">Cancelar Reserva</v-card-title>
        <v-card-text class="text">Tem certeza que deseja cancelar a reserva #{{ itemToCancel?.id }}?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="text" variant="text" @click="closeCancelDialog">Voltar</v-btn>
          <v-btn color="error" variant="elevated" @click="confirmCancel">Sim, Cancelar</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>
