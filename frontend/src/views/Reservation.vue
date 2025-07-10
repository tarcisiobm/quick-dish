<script setup lang="ts">
import { ref, reactive } from 'vue';
import { PhCalendar, PhClock, PhUsers, PhUser, PhPhone, PhEnvelope, PhCheck } from '@phosphor-icons/vue';


const reservationForm = reactive({
  name: '',
  email: '',
  phone: '',
  date: '',
  time: '',
  guests: 2,
  specialRequests: ''
});


const isLoading = ref(false);
const showSuccess = ref(false);


const timeSlots = [
  '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'
];

const guestOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];


const submitReservation = async () => {
  isLoading.value = true;
  
  setTimeout(() => {
    isLoading.value = false;
    showSuccess.value = true;
    
    setTimeout(() => {
      showSuccess.value = false;
      Object.assign(reservationForm, {
        name: '',
        email: '',
        phone: '',
        date: '',
        time: '',
        guests: 2,
        specialRequests: ''
      });
    }, 3000);
  }, 2000);
};

const isFormValid = () => {
  return reservationForm.name && 
         reservationForm.email && 
         reservationForm.phone && 
         reservationForm.date && 
         reservationForm.time;
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

    <!-- Reservation Form -->
    <v-card class="reservation-card" elevation="2">
      <v-card-title class="card-header">
        <PhCalendar size="24" class="color-primary mr-3" />
        <span class="color-title">Nova Reserva</span>
      </v-card-title>

      <v-card-text class="card-content">
        <v-form @submit.prevent="submitReservation">
          <v-row>
            <v-col cols="12">
              <h3 class="section-title color-subtitle mb-4">Informações Pessoais</h3>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="reservationForm.name"
                label="Nome completo"
                :prepend-inner-icon="PhUser"
                variant="outlined"
                required
                :rules="[v => !!v || 'Nome é obrigatório']"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="reservationForm.email"
                label="E-mail"
                type="email"
                :prepend-inner-icon="PhEnvelope"
                variant="outlined"
                required
                :rules="[
                  v => !!v || 'E-mail é obrigatório',
                  v => /.+@.+\..+/.test(v) || 'E-mail deve ser válido'
                ]"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="reservationForm.phone"
                label="Telefone"
                :prepend-inner-icon="PhPhone"
                variant="outlined"
                required
                :rules="[v => !!v || 'Telefone é obrigatório']"
              />
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
                :rules="[v => !!v || 'Horário é obrigatório']"
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

            <!-- Botões -->
            <v-col cols="12" class="d-flex justify-end ga-3">
              <v-btn
                variant="outlined"
                color="text"
                @click="Object.assign(reservationForm, {
                  name: '', email: '', phone: '', date: '', time: '', guests: 2, specialRequests: ''
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

