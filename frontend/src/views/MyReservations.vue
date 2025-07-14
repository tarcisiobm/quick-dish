<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { PhCalendar, PhClock, PhUsers, PhUser, PhPhone, PhEnvelope, PhEye, PhTrash, PhEdit, PhPlus } from '@phosphor-icons/vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

// Estado das reservas
const reservations = ref([]);
const isLoading = ref(false);
const user = ref(null);

// Headers da tabela
const headers = [
  { title: 'Data', key: 'reservation_date', sortable: true },
  { title: 'Horário', key: 'reservation_time', sortable: true },
  { title: 'Pessoas', key: 'number_of_guests', sortable: true },
  { title: 'Observações', key: 'special_requests', sortable: false },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Ações', key: 'actions', sortable: false }
];

// Buscar dados do usuário
const fetchUserData = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (token) {
      const response = await axios.get('http://localhost:8000/api/auth/me', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      user.value = response.data;
    }
  } catch (error) {
    console.error('Erro ao buscar dados do usuário:', error);
  }
};

// Buscar reservas do usuário
const fetchMyReservations = async () => {
  isLoading.value = true;
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('http://localhost:8000/api/reservations/my-reservations', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    reservations.value = response.data.data || response.data;
  } catch (error) {
    console.error('Erro ao buscar reservas:', error);
  } finally {
    isLoading.value = false;
  }
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
      await fetchMyReservations(); // Recarregar lista
    } catch (error) {
      console.error('Erro ao cancelar reserva:', error);
    }
  }
};

// Navegar para nova reserva
const goToNewReservation = () => {
  router.push('/reservations');
};

// Formatar data
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR');
};

// Verificar se a reserva é futura
const isFutureReservation = (reservation) => {
  const reservationDateTime = new Date(`${reservation.reservation_date}T${reservation.reservation_time}`);
  return reservationDateTime > new Date();
};

// Obter status da reserva
const getReservationStatus = (reservation) => {
  if (isFutureReservation(reservation)) {
    return { text: 'Confirmada', color: 'success' };
  } else {
    return { text: 'Realizada', color: 'info' };
  }
};

onMounted(() => {
  fetchUserData();
  fetchMyReservations();
});
</script>

<template>
  <div class="my-reservations-container">
    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title color-title">Minhas Reservas</h1>
      <p class="page-subtitle color-subtitle">
        Visualize e gerencie suas reservas no QuickDish
      </p>
    </div>

    <!-- User Info -->
    <v-card class="user-info-card mb-6" elevation="1" v-if="user">
      <v-card-text>
        <div class="d-flex align-center">
          <v-avatar size="64" class="mr-4" color="primary">
            <span class="text-white text-h5">{{ user.name?.charAt(0) || 'U' }}</span>
          </v-avatar>
          <div>
            <h3 class="color-title mb-1">{{ user.name }}</h3>
            <p class="color-subtitle mb-1">{{ user.email }}</p>
            <p class="color-text" v-if="user.phone">{{ user.phone }}</p>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <!-- Actions -->
    <div class="d-flex justify-between align-center mb-6">
      <div>
        <h2 class="color-title">Suas Reservas</h2>
        <p class="color-subtitle">Total: {{ reservations.length }} reserva(s)</p>
      </div>
      <v-btn
        @click="goToNewReservation"
        color="primary"
        :prepend-icon="PhPlus"
        size="large"
      >
        Nova Reserva
      </v-btn>
    </div>

    <!-- Reservations Table -->
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
        <template v-slot:item.reservation_date="{ item }">
          <div class="d-flex align-center">
            <PhCalendar size="16" class="mr-2 color-primary" />
            <span>{{ formatDate(item.reservation_date) }}</span>
          </div>
        </template>

        <template v-slot:item.reservation_time="{ item }">
          <v-chip size="small" color="primary" variant="outlined">
            <PhClock size="14" class="mr-1" />
            {{ item.reservation_time }}
          </v-chip>
        </template>

        <template v-slot:item.number_of_guests="{ item }">
          <div class="d-flex align-center">
            <PhUsers size="16" class="mr-1" />
            <span>{{ item.number_of_guests }} pessoa(s)</span>
          </div>
        </template>

        <template v-slot:item.special_requests="{ item }">
          <span v-if="item.special_requests" class="text-caption">
            {{ item.special_requests.length > 50 ? item.special_requests.substring(0, 50) + '...' : item.special_requests }}
          </span>
          <span v-else class="text-caption color-subtitle">Nenhuma</span>
        </template>

        <template v-slot:item.status="{ item }">
          <v-chip 
            size="small" 
            :color="getReservationStatus(item).color" 
            variant="tonal"
          >
            {{ getReservationStatus(item).text }}
          </v-chip>
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex ga-2">
            <v-btn
              size="small"
              color="error"
              variant="outlined"
              :icon="PhTrash"
              @click="cancelReservation(item.id)"
              :disabled="!isFutureReservation(item)"
            />
          </div>
        </template>

        <template v-slot:no-data>
          <div class="text-center py-12">
            <PhCalendar size="64" class="color-subtitle mb-4" />
            <h3 class="color-subtitle mb-2">Nenhuma reserva encontrada</h3>
            <p class="color-text mb-4">Você ainda não fez nenhuma reserva</p>
            <v-btn
              @click="goToNewReservation"
              color="primary"
              :prepend-icon="PhPlus"
            >
              Fazer Primeira Reserva
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Statistics -->
    <v-row class="mt-6">
      <v-col cols="12" md="4">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-primary">{{ reservations.length }}</div>
            <div class="stats-label color-subtitle">Total de Reservas</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="stats-card" elevation="2">
          <v-card-text class="text-center">
            <div class="stats-number color-success">
              {{ reservations.filter(r => isFutureReservation(r)).length }}
            </div>
            <div class="stats-label color-subtitle">Próximas Reservas</div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
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
  </div>
</template>

<style lang="scss" scoped>
.my-reservations-container {
  padding: 24px;
  max-width: 1200px;
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

.user-info-card {
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
  .my-reservations-container {
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

