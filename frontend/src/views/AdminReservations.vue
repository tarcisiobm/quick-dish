<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { PhCalendar, PhClock, PhUsers, PhUser, PhPhone, PhEnvelope, PhEye, PhTrash, PhDownload } from '@phosphor-icons/vue';
import axios from 'axios';

// Estado das reservas
const reservations = ref([]);
const isLoading = ref(false);
const filters = reactive({
  date: '',
  status: 'all'
});

// Opções de status
const statusOptions = [
  { value: 'all', title: 'Todas' },
  { value: 'confirmed', title: 'Confirmadas' },
  { value: 'cancelled', title: 'Canceladas' }
];

// Headers da tabela
const headers = [
  { title: 'ID', key: 'id', sortable: true },
  { title: 'Cliente', key: 'user.name', sortable: true },
  { title: 'E-mail', key: 'user.email', sortable: true },
  { title: 'Data', key: 'reservation_date', sortable: true },
  { title: 'Horário', key: 'reservation_time', sortable: true },
  { title: 'Pessoas', key: 'number_of_guests', sortable: true },
  { title: 'Criado em', key: 'created_at', sortable: true },
  { title: 'Ações', key: 'actions', sortable: false }
];

// Buscar reservas
const fetchReservations = async () => {
  isLoading.value = true;
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('http://localhost:8000/api/reservations', {
      headers: {
        'Authorization': `Bearer ${token}`
      },
      params: filters
    });
    reservations.value = response.data.data || response.data;
  } catch (error) {
    console.error('Erro ao buscar reservas:', error);
  } finally {
    isLoading.value = false;
  }
};

// Visualizar detalhes da reserva
const viewReservation = (reservation) => {
  // Implementar modal ou navegação para detalhes
  console.log('Visualizar reserva:', reservation);
};

// Cancelar reserva
const cancelReservation = async (reservationId) => {
  if (confirm('Tem certeza que deseja cancelar esta reserva?')) {
    try {
      const token = localStorage.getItem('auth_token');
      await axios.delete(`http://localhost:8000/api/reservations/${reservationId}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      await fetchReservations(); // Recarregar lista
    } catch (error) {
      console.error('Erro ao cancelar reserva:', error);
    }
  }
};

// Exportar relatório
const exportReport = () => {
  // Implementar exportação para CSV/PDF
  console.log('Exportar relatório');
};

// Formatar data
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR');
};

// Formatar data e hora
const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('pt-BR');
};

onMounted(() => {
  fetchReservations();
});
</script>

<template>
  <div class="admin-reservations-container">
    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title color-title">Relatório de Reservas</h1>
      <p class="page-subtitle color-subtitle">
        Gerencie e visualize todas as reservas do restaurante
      </p>
    </div>

    <!-- Filtros -->
    <v-card class="filters-card mb-6" elevation="1">
      <v-card-text>
        <v-row align="center">
          <v-col cols="12" md="3">
            <v-text-field
              v-model="filters.date"
              label="Filtrar por data"
              type="date"
              :prepend-inner-icon="PhCalendar"
              variant="outlined"
              density="compact"
              @update:model-value="fetchReservations"
            />
          </v-col>

          <v-col cols="12" md="3">
            <v-select
              v-model="filters.status"
              label="Status"
              :items="statusOptions"
              variant="outlined"
              density="compact"
              @update:model-value="fetchReservations"
            />
          </v-col>

          <v-col cols="12" md="3">
            <v-btn
              @click="fetchReservations"
              :loading="isLoading"
              color="primary"
              variant="outlined"
            >
              Atualizar
            </v-btn>
          </v-col>

          <v-col cols="12" md="3" class="text-right">
            <v-btn
              @click="exportReport"
              color="success"
              variant="outlined"
              :prepend-icon="PhDownload"
            >
              Exportar
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Estatísticas -->
    <v-row class="mb-6">
      <v-col cols="12" md="3">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-primary">{{ reservations.length }}</div>
            <div class="stats-label color-subtitle">Total de Reservas</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-success">
              {{ reservations.filter(r => new Date(r.reservation_date) >= new Date()).length }}
            </div>
            <div class="stats-label color-subtitle">Próximas Reservas</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-warning">
              {{ reservations.filter(r => new Date(r.reservation_date).toDateString() === new Date().toDateString()).length }}
            </div>
            <div class="stats-label color-subtitle">Hoje</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-info">
              {{ reservations.reduce((sum, r) => sum + r.number_of_guests, 0) }}
            </div>
            <div class="stats-label color-subtitle">Total de Pessoas</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Tabela de Reservas -->
    <v-card class="reservations-table-card" elevation="2">
      <v-card-title class="card-header">
        <PhCalendar size="24" class="color-primary mr-3" />
        <span class="color-title">Lista de Reservas</span>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="reservations"
        :loading="isLoading"
        class="reservations-table"
        :items-per-page="10"
        :sort-by="[{ key: 'reservation_date', order: 'desc' }]"
      >
        <template v-slot:item.user.name="{ item }">
          <div class="d-flex align-center">
            <v-avatar size="32" class="mr-3" color="primary">
              <span class="text-white">{{ item.user?.name?.charAt(0) || 'U' }}</span>
            </v-avatar>
            <span>{{ item.user?.name || 'N/A' }}</span>
          </div>
        </template>

        <template v-slot:item.reservation_date="{ item }">
          <span>{{ formatDate(item.reservation_date) }}</span>
        </template>

        <template v-slot:item.reservation_time="{ item }">
          <v-chip size="small" color="primary" variant="outlined">
            {{ item.reservation_time }}
          </v-chip>
        </template>

        <template v-slot:item.number_of_guests="{ item }">
          <div class="d-flex align-center">
            <PhUsers size="16" class="mr-1" />
            <span>{{ item.number_of_guests }}</span>
          </div>
        </template>

        <template v-slot:item.created_at="{ item }">
          <span class="text-caption">{{ formatDateTime(item.created_at) }}</span>
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex ga-2">
            <v-btn
              size="small"
              color="primary"
              variant="outlined"
              :icon="PhEye"
              @click="viewReservation(item)"
            />
            <v-btn
              size="small"
              color="error"
              variant="outlined"
              :icon="PhTrash"
              @click="cancelReservation(item.id)"
            />
          </div>
        </template>

        <template v-slot:no-data>
          <div class="text-center py-8">
            <PhCalendar size="48" class="color-subtitle mb-4" />
            <p class="color-subtitle">Nenhuma reserva encontrada</p>
          </div>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<style lang="scss" scoped>
.admin-reservations-container {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 32px;
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.page-subtitle {
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto;
}

.filters-card {
  background-color: rgb(var(--v-theme-alt_background));
  border: 1px solid rgb(var(--v-theme-border));
}

.stats-card {
  background-color: rgb(var(--v-theme-alt_background));
  border: 1px solid rgb(var(--v-theme-border));
  
  .stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 4px;
  }
  
  .stats-label {
    font-size: 0.875rem;
    font-weight: 500;
  }
}

.reservations-table-card {
  background-color: rgb(var(--v-theme-alt_background));
  border: 1px solid rgb(var(--v-theme-border));
}

.card-header {
  background-color: rgb(var(--v-theme-background));
  border-bottom: 1px solid rgb(var(--v-theme-border));
  padding: 20px 24px;
  display: flex;
  align-items: center;
  font-size: 1.25rem;
  font-weight: 600;
}

.reservations-table {
  background-color: transparent;
  
  :deep(.v-data-table__wrapper) {
    background-color: transparent;
  }
  
  :deep(.v-data-table-header) {
    background-color: rgb(var(--v-theme-background));
  }
  
  :deep(.v-data-table-rows-no-data) {
    background-color: transparent;
  }
}

// Responsividade
@media (max-width: 768px) {
  .admin-reservations-container {
    padding: 16px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .stats-card .stats-number {
    font-size: 1.5rem;
  }
}
</style>

