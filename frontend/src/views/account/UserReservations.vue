<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

interface Reservation {
  id: number;
  status: boolean;
  reservation_date: string;
  start_time: string;
  end_time: string;
  guests_count: number;
  table: { number: number } | null;
}

const errorStore = useErrorStore();
const reservations = ref<Reservation[]>([]);
const loading = ref(true);

async function fetchMyReservations() {
  loading.value = true;
  try {
    const response = await api.get('/user/reservations');
    reservations.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

const statusMap = computed(
  () =>
    ({
      true: { text: 'Confirmada', color: 'success' },
      false: { text: 'Cancelada', color: 'error' }
    }) as { [key: string]: { text: string; color: string } }
);

function formatDate(dateString: string) {
  const options: Intl.DateTimeFormatOptions = { day: '2-digit', month: '2-digit', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
}

onMounted(fetchMyReservations);
</script>

<template>
  <div v-if="loading" class="text-center pa-16">
    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
  </div>
  <v-expansion-panels v-else-if="reservations.length > 0" variant="accordion">
    <v-expansion-panel v-for="reservation in reservations" :key="reservation.id">
      <v-expansion-panel-title>
        <v-row no-gutters class="d-flex align-center">
          <v-col cols="4" class="text"> Mesa {{ reservation.table?.number || 'N/A' }} </v-col>
          <v-col cols="4" class="d-flex justify-center">
            <v-chip :color="statusMap[String(reservation.status)]?.color" size="small" variant="tonal">
              {{ statusMap[String(reservation.status)]?.text }}
            </v-chip>
          </v-col>
          <v-col cols="4" class="text-right subtitle">
            {{ formatDate(reservation.reservation_date) }}
          </v-col>
        </v-row>
      </v-expansion-panel-title>
      <v-expansion-panel-text>
        <p class="text"><strong>Horário:</strong> das {{ reservation.start_time }} às {{ reservation.end_time }}</p>
        <p class="text"><strong>Pessoas:</strong> {{ reservation.guests_count }}</p>
      </v-expansion-panel-text>
    </v-expansion-panel>
  </v-expansion-panels>
  <div v-else class="text-center py-16">
    <v-icon icon="mdi-calendar-remove-outline" size="64" color="grey"></v-icon>
    <p class="text text-h6 color-subtitle mt-4">Você ainda não fez nenhuma reserva.</p>
  </div>
</template>
