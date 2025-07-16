<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { PhCalendar, PhClock, PhUsers, PhCheck } from '@phosphor-icons/vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const reservationForm = reactive({
  date: '',
  time: '',
  guests: 2,
  specialRequests: ''
});

const isLoading = ref(false);
const showSuccess = ref(false);
const user = ref(null);

const timeSlots = [
  '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'
];

const guestOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

const submitReservation = async () => {
  isLoading.value = true;
  showSuccess.value = false;

  if (!authStore.user) {
    alert('Erro: Usuário não logado. Por favor, faça login para fazer uma reserva.');
    isLoading.value = false;
    return;
  }

  try {
    const payload = {
      reservation_date: reservationForm.date,
      reservation_time: reservationForm.time,
      number_of_guests: reservationForm.guests,
      special_requests: reservationForm.specialRequests,
      user_id: authStore.user.id,
      user_name: authStore.user.name,
      user_email: authStore.user.email,
      user_phone: authStore.user.phone,
    };

    const token = localStorage.getItem('auth_token');
    const headers = {
      'Content-Type': 'application/json',
    };

    // if (token) {
    //   headers as any['Authorization'] ; `Bearer ${token}`;
    // }

    const response = await axios.post('http://localhost:8000/api/reservations', payload, { headers });

    console.log('Reserva criada com sucesso:', response.data);
    showSuccess.value = true;

    Object.assign(reservationForm, {
      date: '',
      time: '',
      guests: 2,
      specialRequests: ''
    });

    setTimeout(() => {
      showSuccess.value = false;
    }, 5000);

  } catch (error: any) {
    console.error('Erro ao criar reserva:', error.response ? error.response.data : error.message);
    alert('Erro ao criar reserva: ' + (error.response && error.response.data.message ? error.response.data.message : 'Verifique o console para mais detalhes.'));
  } finally {
    isLoading.value = false;
  }
};

const isFormValid = () => {
  return reservationForm.date && reservationForm.time && reservationForm.guests > 0;
};
</script>

<template>
  <div class="reservations-container">
    <div class="page-header">
      <h1 class="page-title color-title">Reservas</h1>
      <p class="page-subtitle color-subtitle">
        Reserve sua mesa no QuickDish e desfrute de uma experiência gastronômica única
      </p>
    </div>

    <v-alert
      v-if="showSuccess"
      type="success"
      variant="tonal"
      class="mb-6"
      :icon="PhCheck"
    >
      <template v-slot:title>
        Reserva confirmada!
      </template>
      Sua reserva foi realizada com sucesso. Você receberá um e-mail de confirmação em breve.
    </v-alert>

    <v-card class="reservation-card" elevation="2">
      <v-card-title class="card-header">
        <PhCalendar size="24" class="color-primary mr-3" />
        <span class="color-title">Nova Reserva</span>
      </v-card-title>

      <v-card-text class="card-content">
        <v-form @submit.prevent="submitReservation">
          <v-row>
            <v-col cols="12" v-if="authStore.user">
              <h3 class="section-title color-subtitle mb-4">Dados da Reserva</h3>
              <div class="user-info mb-4">
                <p><strong>Nome:</strong> {{ authStore.user?.name }}</p>
                <p><strong>E-mail:</strong> {{ authStore.user?.email }}</p>
                <p v-if="authStore.user?.phone"><strong>Telefone:</strong> {{ authStore.user?.phone }}</p>
              </div>
            </v-col>

            <v-col cols="12">
              <h3 class="section-title color-subtitle mb-4 mt-4">Detalhes da Reserva</h3>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="reservationForm.date"
                label="Data"
                type="date"
                :prepend-inner-icon="PhCalendar"
                variant="outlined"
                required
                :min="new Date().toISOString().split('T')[0]"
                :rules="[v => !!v || 'Data é obrigatória']"
              />
            </v-col>

            <v-col cols="12" md="4">
              <v-select
                v-model="reservationForm.time"
                label="Horário"
                :items="timeSlots"
                :prepend-inner-icon="PhClock"
                variant="outlined"
                required
                :rules="[v => !!v || 'Horário é obrigatória']"
              />
            </v-col>

            <v-col cols="12" md="4">
              <v-select
                v-model="reservationForm.guests"
                label="Número de pessoas"
                :items="guestOptions"
                :prepend-inner-icon="PhUsers"
                variant="outlined"
                required
              />
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="reservationForm.specialRequests"
                label="Observações especiais (opcional)"
                placeholder="Aniversário, restrições alimentares, preferências de mesa..."
                variant="outlined"
                rows="3"
                no-resize
              />
            </v-col>

            <v-col cols="12" class="d-flex justify-end ga-3">
              <v-btn
                variant="outlined"
                color="text"
                @click="Object.assign(reservationForm, {
                  date: '', time: '', guests: 2, specialRequests: ''
                })"
              >
                Limpar
              </v-btn>
              
              <v-btn
                type="submit"
                :loading="isLoading"
                :disabled="!isFormValid()"
                class="reservation-btn"
              >
                <template v-slot:prepend>
                  <PhCheck size="20" />
                </template>
                Confirmar Reserva
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>

    <v-card class="info-card mt-6" elevation="1">
      <v-card-text>
        <h3 class="color-subtitle mb-3">Informações Importantes</h3>
        <v-list class="info-list">
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="primary" size="small">mdi-clock-outline</v-icon>
            </template>
            <v-list-item-title class="color-text">
              Horário de funcionamento: 18h às 23h
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="primary" size="small">mdi-calendar-check</v-icon>
            </template>
            <v-list-item-title class="color-text">
              Reservas podem ser feitas com até 30 dias de antecedência
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="primary" size="small">mdi-phone</v-icon>
            </template>
            <v-list-item-title class="color-text">
              Para grupos acima de 10 pessoas, entre em contato: (11) 99999-9999
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <v-icon color="primary" size="small">mdi-cancel</v-icon>
            </template>
            <v-list-item-title class="color-text">
              Cancelamentos devem ser feitos com pelo menos 2 horas de antecedência
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>
  </div>
</template>

<style lang="scss" scoped>
.reservations-container {
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

.user-info {
  background-color: rgb(var(--v-theme-background));
  padding: 16px;
  border-radius: 8px;
  border: 1px solid rgb(var(--v-theme-border));
  
  p {
    margin: 4px 0;
    color: rgb(var(--v-theme-text));
  }
}

.reservation-card {
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

.card-content {
  padding: 32px 24px;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 600;
  position: relative;
  padding-left: 12px;
  
  &::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 20px;
    background-color: rgb(var(--v-theme-primary));
    border-radius: 2px;
  }
}

.reservation-btn {
  min-width: 180px;
  height: 48px;
  font-weight: 600;
}

.info-card {
  background-color: rgb(var(--v-theme-alt_background));
  border: 1px solid rgb(var(--v-theme-border));
}

.info-list {
  background-color: transparent;
  
  .v-list-item {
    padding-left: 0;
    min-height: 40px;
  }
}

@media (max-width: 768px) {
  .reservations-container {
    padding: 16px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .card-content {
    padding: 24px 16px;
  }
}
</style>