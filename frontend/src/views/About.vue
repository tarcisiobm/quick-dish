<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { PhForkKnife, PhMapPin, PhPhone, PhEnvelope } from '@phosphor-icons/vue';

// Define a interface para os dados do restaurante (se forem carregados de uma API)
interface RestaurantInfo {
  name: string;
  description: string;
  history: string;
  address: string;
  phone: string;
  email: string;
  image_url: string;
  mission: string;
  values: string[];
}

const restaurantInfo = ref<RestaurantInfo | null>(null);
const isLoading = ref(true);
const errorMessage = ref<string | null>(null);

// Função para buscar dados do restaurante do backend (se aplicável)
const fetchRestaurantInfo = async () => {
  isLoading.value = true;
  errorMessage.value = null;
  try {
    // Exemplo de URL da API. Você precisaria criar este endpoint no Laravel.
    // Se o conteúdo for estático, esta parte e o 'restaurantInfo.value' podem ser removidos.
    const response = await axios.get('http://localhost:8000/api/about-us');
    restaurantInfo.value = response.data;
  } catch (error: any) {
    console.error('Erro ao buscar informações do restaurante:', error);
    errorMessage.value = 'Não foi possível carregar as informações do restaurante. Por favor, tente novamente mais tarde.';
    // Conteúdo estático de fallback se a API falhar ou não existir
    restaurantInfo.value = {
      name: 'QuickDish',
      description: 'Bem-vindo ao QuickDish, onde a culinária encontra a paixão. Nossa jornada começou em 2020 com a visão de trazer sabores autênticos e inovação para a sua mesa. Cada prato é uma celebração de ingredientes frescos e técnicas aprimoradas.',
      history: 'Fundado pelo Chef João Silva, o QuickDish rapidamente se tornou um ponto de referência gastronômico na cidade, conhecido por sua atmosfera acolhedora e pratos inesquecíveis que combinam tradição e modernidade.',
      address: 'Rua da Gastronomia, 123, Centro - Muriaé, MG',
      phone: '(32) 98765-4321',
      email: 'contato@quickdish.com.br',
      image_url: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', // Imagem de exemplo
      mission: 'Nossa missão é proporcionar uma experiência gastronômica memorável, combinando inovação, qualidade e um serviço excepcional, celebrando a arte da culinária em cada detalhe.',
      values: ['Qualidade', 'Paixão', 'Inovação', 'Hospitalidade', 'Sustentabilidade']
    };
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchRestaurantInfo();
});
</script>

<template>
  <div class="about-container">
    <div class="page-header">
      <h1 class="page-title color-title">Sobre o QuickDish</h1>
      <p class="page-subtitle color-subtitle">
        Descubra a paixão e a história por trás de cada sabor em nosso restaurante.
      </p>
    </div>

    <v-card class="about-card" elevation="2" v-if="!isLoading && restaurantInfo">
      <v-img :src="restaurantInfo.image_url" class="about-hero-image"></v-img>

      <v-card-text class="card-content">
        <h2 class="section-title color-title mb-4">Nossa Essência</h2>
        <p class="color-text mb-6">{{ restaurantInfo.description }}</p>

        <h2 class="section-title color-title mb-4 mt-6">Nossa História</h2>
        <p class="color-text mb-6">{{ restaurantInfo.history }}</p>

        <h2 class="section-title color-title mb-4 mt-6">Missão e Valores</h2>
        <p class="color-text mb-2">{{ restaurantInfo.mission }}</p>
        <v-list class="info-list">
          <v-list-item v-for="(value, index) in restaurantInfo.values" :key="index" density="compact">
            <template v-slot:prepend>
              <v-icon color="primary" size="small">mdi-check-circle-outline</v-icon>
            </template>
            <v-list-item-title class="color-text">{{ value }}</v-list-item-title>
          </v-list-item>
        </v-list>

        <h2 class="section-title color-title mb-4 mt-6">Onde Estamos</h2>
        <v-list class="info-list">
          <v-list-item>
            <template v-slot:prepend>
              <PhMapPin size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">{{ restaurantInfo.address }}</v-list-item-title>
          </v-list-item>
          <v-list-item>
            <template v-slot:prepend>
              <PhPhone size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">{{ restaurantInfo.phone }}</v-list-item-title>
          </v-list-item>
          <v-list-item>
            <template v-slot:prepend>
              <PhEnvelope size="20" class="color-primary" />
            </template>
            <v-list-item-title class="color-text">{{ restaurantInfo.email }}</v-list-item-title>
          </v-list-item>
        </v-list>

        <v-btn
          color="primary"
          size="large"
          class="mt-8"
          block
          :prepend-icon="PhForkKnife"
          @click="$router.push('/reservations')"
        >
          Faça sua Reserva!
        </v-btn>
      </v-card-text>
    </v-card>

    <v-progress-circular
      v-else-if="isLoading"
      indeterminate
      color="primary"
      class="d-flex justify-center my-8"
    ></v-progress-circular>

    <v-alert
      v-else-if="errorMessage"
      type="error"
      variant="tonal"
      class="my-6"
    >
      {{ errorMessage }}
    </v-alert>

  </div>
</template>

<style lang="scss" scoped>
.about-container {
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

.about-card {
  background-color: rgb(var(--v-theme-alt_background));
  border: 1px solid rgb(var(--v-theme-border));
  overflow: hidden; // Garante que a imagem não vaze
}

.about-hero-image {
  height: 300px;
  object-fit: cover;
  width: 100%;
}

.card-content {
  padding: 32px 24px;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  position: relative;
  padding-left: 16px;
  margin-bottom: 16px;

  &::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 30px;
    background-color: rgb(var(--v-theme-primary));
    border-radius: 3px;
  }
}

.color-title {
  color: rgb(var(--v-theme-title));
}

.color-subtitle {
  color: rgb(var(--v-theme-subtitle));
}

.color-text {
  color: rgb(var(--v-theme-text));
}

.color-primary {
  color: rgb(var(--v-theme-primary));
}

.info-list {
  background-color: transparent;
  .v-list-item {
    padding-left: 0;
    min-height: 40px;
  }
  .v-list-item-title {
    font-size: 1rem;
  }
}

// Responsividade
@media (max-width: 768px) {
  .about-container {
    padding: 16px;
  }

  .page-title {
    font-size: 2rem;
  }

  .section-title {
    font-size: 1.25rem;
    padding-left: 12px;
    &::before {
      width: 4px;
      height: 25px;
    }
  }

  .about-hero-image {
    height: 200px;
  }

  .card-content {
    padding: 24px 16px;
  }
}
</style>