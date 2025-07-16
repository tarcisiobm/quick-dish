<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { PhCalendar, PhClock, PhUsers, PhEye, PhTrash, PhDownload } from '@phosphor-icons/vue';
import axios from 'axios';

import { useAuthStore } from '@/stores/auth';
// Importação de DateTimeOptions não é necessária aqui, a menos que você a use para algo específico do i18n
// e esteja aplicando no template. Para tipagem de dados, 'string' é o mais adequado.
// import type { DateTimeOptions } from 'vue-i18n'; // <-- Pode ser removida se não for usada para i18n complexo

// Interfaces de Tipagem
interface User {
  id: number;
  name: string;
  email: string;
  phone?: string | null;
  // Adicione outras propriedades do usuário se necessário
}

// **CORREÇÃO AQUI: reservation_date deve ser string**
interface Reservation {
  id: number;
  reservation_date: string; // Data no formato string (ex: 'YYYY-MM-DD')
  reservation_time: string; // Horário no formato string (ex: 'HH:MM')
  number_of_guests: number;
  special_requests: string | null;
  status: string;
  created_at: string; // Data de criação (provavelmente string ISO 8601)
  user: User; // A reserva tem um objeto de usuário aninhado
  // Adicione outras propriedades da reserva que sua API possa retornar
}

// Estado das reservas - **CORREÇÃO AQUI: tipagem explícita**
const reservations = ref<Reservation[]>([]);
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
  { title: 'Cliente', key: 'user.name', sortable: true }, // Acessa propriedade aninhada
  { title: 'E-mail', key: 'user.email', sortable: true }, // Acessa propriedade aninhada
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
    // Mapeia os dados para garantir a tipagem e o tratamento de valores nulos/string-numérica
    const rawData = response.data.data || response.data;
    reservations.value = rawData.map((r: any) => ({
      id: r.id,
      reservation_date: r.reservation_date || '', // Garante que é string
      reservation_time: r.reservation_time || '', // Garante que é string
      number_of_guests: Number(r.number_of_guests) || 0, // Garante que é número
      special_requests: r.special_requests || null,
      status: r.status || 'unknown', // Default para status
      created_at: r.created_at || '', // Garante que é string
      user: { // Garante a estrutura do objeto user
        id: r.user?.id || 0,
        name: r.user?.name || 'N/A',
        email: r.user?.email || 'N/A',
        phone: r.user?.phone || null,
      }
    }));

  } catch (error) {
    console.error('Erro ao buscar reservas:', error);
    reservations.value = []; // Limpa as reservas em caso de erro
  } finally {
    isLoading.value = false;
  }
};

// Visualizar detalhes da reserva
const viewReservation = (reservation: Reservation) => { // Tipagem do parâmetro
  console.log('Visualizar reserva:', reservation);
  // Implementar modal ou navegação para detalhes
  // Ex: router.push(`/admin/reservations/${reservation.id}`);
};

// Cancelar reserva
const cancelReservation = async (reservationId: number) => { // Tipagem do parâmetro
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
      alert('Erro ao cancelar reserva. Por favor, tente novamente.');
    }
  }
};

// Exportar relatório
const exportReport = () => {
  console.log('Exportar relatório');
  // Lógica para exportar, por exemplo, gerando um CSV
  // Exemplo básico de CSV:
  let csvContent = "data:text/csv;charset=utf-8,";
  csvContent += headers.map(h => h.title).join(',') + "\r\n"; // Cabeçalhos
  reservations.value.forEach(row => {
    // Adapte aqui para os campos que você quer no CSV
    const rowData = [
      row.id,
      `"${row.user?.name || 'N/A'}"`, // Aspas para nomes com vírgulas
      row.user?.email || 'N/A',
      formatDate(row.reservation_date),
      row.reservation_time,
      row.number_of_guests,
      formatDateTime(row.created_at)
    ];
    csvContent += rowData.join(',') + "\r\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "reservas_relatorio.csv");
  document.body.appendChild(link); // Required for Firefox
  link.click(); // This will download the data file named "my_data.csv".
  document.body.removeChild(link); // Clean up
};

// Formatar data (mantida como estava, com pequena melhoria para segurança)
const formatDate = (dateString: string | undefined) => { // Permite string ou undefined
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
  } catch {
    return dateString; // Em caso de erro, retorna a string original
  }
};

// Formatar data e hora (mantida como estava, com pequena melhoria para segurança)
const formatDateTime = (dateString: string | undefined) => { // Permite string ou undefined
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleString('pt-BR', { timeZone: 'UTC' });
  } catch {
    return dateString; // Em caso de erro, retorna a string original
  }
};

onMounted(() => {
  fetchReservations();
});
</script>

<template>
  <div class="admin-reservations-container">
    <div class="page-header">
      <h1 class="page-title color-title">Relatório de Reservas</h1>
      <p class="page-subtitle color-subtitle">
        Gerencie e visualize todas as reservas do restaurante
      </p>
    </div>

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
              item-title="title"
              item-value="value"
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
              {{ reservations.filter(r => new Date(r.reservation_date).setHours(0,0,0,0) >= new Date().setHours(0,0,0,0)).length }}
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

        <template v-slot:item.user.email="{ item }">
          <span>{{ item.user?.email || 'N/A' }}</span>
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