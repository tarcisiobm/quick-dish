<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import {
  PhCalendar,
  PhClock,
  PhUsers,
  PhUser,
  PhPhone,
  PhEnvelope,
  PhCheck,
  PhWarningCircle, // Para o ícone de erro
  PhCalendarCheck,
  PhPhoneCall // Ícone para telefone, se preferir
} from '@phosphor-icons/vue';
import axios from 'axios'; // Importar Axios, agora que já está instalado!

// SIMULAÇÃO: Estado do usuário logado.
// No seu projeto real, isso viria de um store (Pinia/Vuex) ou sistema de autenticação.
// Ajuste 'isLoggedIn' para 'true' ou 'false' conforme a necessidade de teste:
// - 'true': campos de nome/email/telefone serão preenchidos e desabilitados.
// - 'false': campos de nome/email/telefone precisarão ser preenchidos manualmente.
const currentUser = ref({
  name: 'João Silva',
  email: 'joao.silva@example.com',
  phone: '11987654321',
  isLoggedIn: false, // <-- Mude para true para testar o comportamento de usuário logado
});

const reservationForm = reactive({
  // Preenche com dados do usuário logado, se houver, ou deixa vazio
  name: currentUser.value.isLoggedIn ? currentUser.value.name : '',
  email: currentUser.value.isLoggedIn ? currentUser.value.email : '',
  phone: currentUser.value.isLoggedIn ? currentUser.value.phone : '',
  date: '',
  time: '',
  guests: 2, // Valor padrão
  specialRequests: ''
});

const isLoading = ref(false);       // Controla o estado de carregamento do botão
const showSuccess = ref(false);     // Controla a exibição da mensagem de sucesso
const showError = ref(false);       // Controla a exibição da mensagem de erro
const errorMessage = ref('');       // Mensagem detalhada do erro para o usuário

const timeSlots = [
  '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'
];

const guestOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// URL base da sua API Laravel. IMPORTANTE: Verifique se 'http://127.0.0.1:8000' é a porta correta do seu `php artisan serve`.
const API_BASE_URL = 'http://127.0.0.1:8000/api';

const submitReservation = async () => {
  isLoading.value = true;    // Ativa o estado de carregamento
  showSuccess.value = false; // Garante que a mensagem de sucesso está oculta
  showError.value = false;   // Garante que a mensagem de erro está oculta
  errorMessage.value = '';   // Limpa qualquer mensagem de erro anterior

  try {
    // Faz a requisição POST para o endpoint de reservas da sua API Laravel
    const response = await axios.post(`${API_BASE_URL}/reservations`, {
      name: reservationForm.name,
      email: reservationForm.email,
      phone: reservationForm.phone,
      date: reservationForm.date,
      time: reservationForm.time,
      guests: reservationForm.guests,
      specialRequests: reservationForm.specialRequests,
    });

    if (response.status === 201) { // Laravel retorna 201 Created para sucesso na criação
      showSuccess.value = true; // Exibe mensagem de sucesso

      // Reseta os campos do formulário para nova reserva
      Object.assign(reservationForm, {
        date: '',
        time: '',
        guests: 2,
        specialRequests: ''
      });
      // Se o usuário NÃO está logado, também reseta os dados pessoais
      if (!currentUser.value.isLoggedIn) {
        Object.assign(reservationForm, {
          name: '',
          email: '',
          phone: '',
        });
      }
      setTimeout(() => { showSuccess.value = false; }, 3000); // Esconde a mensagem após 3 segundos
    }
  } catch (error: any) {
    console.error('Erro ao enviar reserva:', error); // Loga o erro completo no console do navegador
    showError.value = true; // Ativa a exibição da mensagem de erro

    // Tenta extrair uma mensagem de erro útil da resposta da API Laravel
    if (error.response && error.response.data) {
      if (error.response.data.errors) {
        // Se houver erros de validação (geralmente status 422), mostra-os formatados
        const validationErrors = Object.values(error.response.data.errors).flat();
        errorMessage.value = validationErrors.join('\n'); // Junta as mensagens de erro por campo
      } else if (error.response.data.message) {
        // Se houver uma mensagem de erro geral do backend
        errorMessage.value = error.response.data.message;
      } else {
        errorMessage.value = 'Ocorreu um erro desconhecido na API. Verifique o console.';
      }
    } else {
      errorMessage.value = 'Não foi possível conectar ao servidor. Verifique sua conexão ou a URL da API.';
    }
    setTimeout(() => { showError.value = false; }, 5000); // Esconde a mensagem de erro após 5 segundos
  } finally {
    isLoading.value = false; // Desativa o estado de carregamento, finalize a ação
  }
};

// Computed property para validar o formulário no frontend
const isFormValid = computed(() => {
  // A validação para campos pessoais depende se o usuário está logado ou não
  const personalFieldsValid = currentUser.value.isLoggedIn ||
                              (reservationForm.name && reservationForm.email && reservationForm.phone && /.+@.+\..+/.test(reservationForm.email));
  
  // Valida os campos da reserva em si
  return personalFieldsValid &&
         reservationForm.date &&
         reservationForm.time;
});

// Computed property para controlar se os campos pessoais devem ser desabilitados
const disablePersonalFields = computed(() => currentUser.value.isLoggedIn);
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

    <v-alert
      v-if="showError"
      type="error"
      variant="tonal"
      class="mb-6"
      :icon="PhWarningCircle"
    >
      <template v-slot:title>
        Erro ao Reservar
      </template>
      <pre>{{ errorMessage }}</pre> </v-alert>

    <v-card class="reservation-card" elevation="2">
      <v-card-title class="card-header">
        <PhCalendar size="24" class="color-primary mr-3" />
        <span class="color-title">Nova Reserva</span>
      </v-card-title>

      <v-card-text class="card-content">
        <form @submit.prevent="submitReservation">
          <div class="row">
            <div class="col-12">
              <h3 class="section-title color-subtitle mb-4">Informações Pessoais</h3>
            </div>

            <div class="col-12 col-md-6">
              <div class="input-wrapper">
                <PhUser class="input-icon" />
                <input
                  type="text"
                  v-model="reservationForm.name"
                  placeholder="Nome completo"
                  class="form-input"
                  :disabled="disablePersonalFields"
                  required
                />
                <span v-if="!reservationForm.name && !disablePersonalFields && !isLoading" class="error-message">Nome é obrigatório</span>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="input-wrapper">
                <PhEnvelope class="input-icon" />
                <input
                  type="email"
                  v-model="reservationForm.email"
                  placeholder="E-mail"
                  class="form-input"
                  :disabled="disablePersonalFields"
                  required
                />
                <span v-if="!reservationForm.email && !disablePersonalFields && !isLoading" class="error-message">E-mail é obrigatório</span>
                <span v-else-if="!/.+@.+\..+/.test(reservationForm.email) && reservationForm.email && !disablePersonalFields && !isLoading" class="error-message">E-mail deve ser válido</span>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="input-wrapper">
                <PhPhone class="input-icon" />
                <input
                  type="text"
                  v-model="reservationForm.phone"
                  placeholder="Telefone"
                  class="form-input"
                  :disabled="disablePersonalFields"
                  required
                />
                <span v-if="!reservationForm.phone && !disablePersonalFields && !isLoading" class="error-message">Telefone é obrigatório</span>
              </div>
            </div>

            <div class="col-12">
              <h3 class="section-title color-subtitle mb-4 mt-4">Detalhes da Reserva</h3>
            </div>

            <div class="col-12 col-md-4">
              <div class="input-wrapper">
                <PhCalendar class="input-icon" />
                <input
                  type="date"
                  v-model="reservationForm.date"
                  placeholder="Data"
                  class="form-input"
                  :min="new Date().toISOString().split('T')[0]"
                  required
                />
                <span v-if="!reservationForm.date && !isLoading" class="error-message">Data é obrigatória</span>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="input-wrapper">
                <PhClock class="input-icon" />
                <select
                  v-model="reservationForm.time"
                  class="form-select"
                  required
                >
                  <option value="" disabled>Selecione um horário</option>
                  <option v-for="time in timeSlots" :key="time" :value="time">{{ time }}</option>
                </select>
                <span v-if="!reservationForm.time && !isLoading" class="error-message">Horário é obrigatório</span>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="input-wrapper">
                <PhUsers class="input-icon" />
                <select
                  v-model="reservationForm.guests"
                  class="form-select"
                  required
                >
                  <option v-for="num in guestOptions" :key="num" :value="num">{{ num }} pessoas</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <textarea
                v-model="reservationForm.specialRequests"
                placeholder="Observações especiais (opcional): Aniversário, restrições alimentares, preferências de mesa..."
                class="form-textarea"
                rows="3"
              />
            </div>

            <div class="col-12 form-actions">
              <button
                type="button"
                class="btn btn-outline"
                @click="Object.assign(reservationForm, {
                  date: '', time: '', guests: 2, specialRequests: ''
                }); if (!currentUser.isLoggedIn) { Object.assign(reservationForm, { name: '', email: '', phone: '' }); }"
              >
                Limpar
              </button>
              
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isLoading || !isFormValid"
              >
                <span v-if="isLoading" class="spinner"></span>
                <template v-else>
                  <PhCheck size="20" />
                  Confirmar Reserva
                </template>
              </button>
            </div>
          </div>
        </form>
      </v-card-text>
    </v-card>

    <v-card class="info-card mt-6" elevation="1">
      <v-card-text>
        <h3 class="color-subtitle mb-3">Informações Importantes</h3>
        <v-list class="info-list">
          <v-list-item>
            <template v-slot:prepend>
              <PhClock size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">
              Horário de funcionamento: 18h às 23h
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <PhCalendarCheck size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">
              Reservas podem ser feitas com até 30 dias de antecedência
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <PhPhoneCall size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">
              Para grupos acima de 10 pessoas, entre em contato: (11) 99999-9999
            </v-list-item-title>
          </v-list-item>
          
          <v-list-item>
            <template v-slot:prepend>
              <PhWarningCircle size="20" class="color-primary" />
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

/* ATENÇÃO: Se você não usa Vuetify, estas cores e estilos são exemplos.
   Ajuste-as conforme o seu sistema de design.
*/
.reservation-card {
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  box-shadow: 0px 2px 4px rgba(0,0,0,0.1);
  border-radius: 8px;
}

.card-header {
  background-color: #eee;
  border-bottom: 1px solid #ccc;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  font-size: 1.25rem;
  font-weight: 600;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
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
    background-color: #ff6f00;
    border-radius: 2px;
  }
}

/* Estilos para campos de formulário padrão (substituindo Vuetify) */
.input-wrapper {
  position: relative;
  margin-bottom: 20px;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  z-index: 1;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 10px 10px 10px 40px; /* Espaço para o ícone */
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.2s ease-in-out;

  &:focus {
    border-color: #ff6f00;
  }
}

.form-textarea {
  padding-left: 10px; /* Textarea não precisa de ícone */
}

.error-message {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: 4px;
  display: block;
}

/* Estilos para botões padrão (substituindo Vuetify) */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

.btn {
  padding: 12px 24px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
}

.btn-outline {
  background-color: transparent;
  border: 1px solid #666;
  color: #666;

  &:hover:not(:disabled) {
    background-color: #f0f0f0;
  }
}

.btn-primary {
  background-color: #ff6f00;
  border: 1px solid #ff6f00;
  color: #fff;

  &:hover:not(:disabled) {
    background-color: #e06000;
  }
}

.spinner {
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 2px solid #fff;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
  display: inline-block;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}


.reservation-btn {
  min-width: 180px;
  height: 48px;
  font-weight: 600;
}

.info-card {
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  box-shadow: 0px 1px 3px rgba(0,0,0,0.05);
  border-radius: 8px;
}

.info-list {
  background-color: transparent;
  
  .v-list-item {
    padding-left: 0;
    min-height: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin-left: -12px;
  margin-right: -12px;
}

.col-12 {
  width: 100%;
  padding-left: 12px;
  padding-right: 12px;
}

.col-md-6 {
  width: 100%;
  padding-left: 12px;
  padding-right: 12px;
}

.col-md-4 {
  width: 100%;
  padding-left: 12px;
  padding-right: 12px;
}

@media (min-width: 768px) {
  .col-md-6 {
    width: 50%;
  }
  .col-md-4 {
    width: 33.3333%;
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

.color-title { color: #333; }
.color-subtitle { color: #666; }
.color-primary { color: #ff6f00; }
.color-text { color: #444; }
</style>