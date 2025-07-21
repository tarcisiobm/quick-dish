<script setup lang="ts">
import { ref, reactive } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

interface Table {
  id: number;
  number: number;
  capacity: number;
}

interface ReservationForm {
  guests_count: number | null;
  reservation_date: string;
  start_time: string;
  end_time: string;
  table_id: number | null;
  notes: string;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const authStore = useAuthStore();
const router = useRouter();

const form = reactive<ReservationForm>({
  guests_count: null,
  reservation_date: new Date().toISOString().split('T')[0],
  start_time: '19:00',
  end_time: '21:00',
  table_id: null,
  notes: ''
});

const availableTables = ref<Table[]>([]);
const loadingTables = ref(false);
const submitting = ref(false);

async function checkAvailability() {
  loadingTables.value = true;
  form.table_id = null;
  availableTables.value = [];
  try {
    const params = {
      guests_count: form.guests_count,
      reservation_date: form.reservation_date,
      start_time: form.start_time,
      end_time: form.end_time
    };
    const response = await api.get('/available-tables', { params });
    availableTables.value = response.data.data;
    if (availableTables.value.length === 0) {
      snackbarStore.warning('Nenhuma mesa disponível para os critérios selecionados.');
    }
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loadingTables.value = false;
  }
}

async function submitReservation() {
  if (!form.table_id) {
    snackbarStore.error('Por favor, selecione uma mesa disponível.');
    return;
  }
  submitting.value = true;
  try {
    const payload = { ...form, user_id: authStore.user?.id };
    const response = await api.post('/reservations', payload);
    snackbarStore.success(response.data.message || 'Reserva realizada com sucesso!');
    router.push('/my-account');
  } catch (error) {
    errorStore.handle(error);
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="title text-h5 pa-4">Faça sua Reserva</v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <p class="text color-subtitle mb-6">Preencha os dados abaixo para encontrar uma mesa.</p>
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field v-model="form.guests_count" label="Número de Pessoas" type="number" min="1" prepend-inner-icon="mdi-account-group"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field v-model="form.reservation_date" label="Data da Reserva" type="date"></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field v-model="form.start_time" label="Horário de Chegada" type="time"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field v-model="form.end_time" label="Horário de Saída" type="time"></v-text-field>
              </v-col>
            </v-row>
            <v-btn block @click="checkAvailability" :loading="loadingTables" class="my-4"> Verificar Disponibilidade </v-btn>

            <div v-if="availableTables.length > 0">
              <v-divider class="my-4"></v-divider>
              <p class="subtitle mb-2">Mesas disponíveis encontradas!</p>
              <v-select v-model="form.table_id" :items="availableTables" item-title="number" item-value="id" label="Selecione sua mesa">
                <template v-slot:item="{ props, item }">
                  <v-list-item v-bind="props" :title="`Mesa ${item.raw.number}`" :subtitle="`Capacidade: ${item.raw.capacity} pessoas`"></v-list-item>
                </template>
              </v-select>
              <v-textarea v-model="form.notes" label="Observações (opcional)" class="mt-4"></v-textarea>
            </div>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn size="large" @click="submitReservation" :disabled="!form.table_id" :loading="submitting"> Confirmar Reserva </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
