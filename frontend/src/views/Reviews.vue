<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';
import { useAuthStore } from '@/stores/auth';

interface Review {
  id: number;
  rating: number;
  comment: string | null;
  created_at: string;
  user: {
    name: string;
    avatar: string | null;
  };
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const authStore = useAuthStore();

const reviews = ref<Review[]>([]);
const loading = ref(true);

const form = ref({
  rating: 0,
  comment: ''
});
const isSubmitting = ref(false);

async function fetchReviews() {
  loading.value = true;
  try {
    const response = await api.get('/reviews');
    reviews.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

async function submitReview() {
  if (form.value.rating === 0) {
    snackbarStore.error('Por favor, selecione uma nota de 1 a 5 estrelas.');
    return;
  }
  isSubmitting.value = true;
  try {
    const response = await api.post('/reviews', form.value);
    snackbarStore.success(response.data.message || 'Avaliação enviada com sucesso!');
    form.value = { rating: 0, comment: '' };
    fetchReviews();
  } catch (error) {
    errorStore.handle(error);
  } finally {
    isSubmitting.value = false;
  }
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('pt-BR');
}

onMounted(fetchReviews);
</script>

<template>
  <v-container>
    <v-row justify="center" class="text-center mt-4 mb-10">
      <v-col cols="12" md="8">
        <h1 class="title text-h3 mb-2">O que Nossos Clientes Dizem</h1>
        <p class="text text-body-1 color-subtitle">A sua opinião é muito importante para nós e ajuda outros clientes a nos conhecerem melhor.</p>
      </v-col>
    </v-row>

    <v-row v-if="authStore.isAuthenticated" justify="center" class="mb-12">
      <v-col cols="12" md="8">
        <v-card variant="outlined">
          <v-card-title class="subtitle">Deixe sua avaliação</v-card-title>
          <v-card-text>
            <div class="text-center mb-2">
              <v-rating v-model="form.rating" hover half-increments color="alert" active-color="alert" density="comfortable"></v-rating>
            </div>
            <v-textarea v-model="form.comment" label="Seu comentário (opcional)" rows="3" no-resize variant="outlined"></v-textarea>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn :loading="isSubmitting" @click="submitReview">Enviar Avaliação</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <v-divider v-if="authStore.isAuthenticated" class="mb-12"></v-divider>

    <div v-if="loading" class="text-center">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>
    <div v-else-if="reviews.length > 0">
      <v-row>
        <v-col v-for="review in reviews" :key="review.id" cols="12" md="6">
          <v-card variant="tonal" class="fill-height">
            <v-card-text>
              <div class="d-flex align-center mb-4">
                <v-avatar class="mr-4">
                  <v-img :src="review.user.avatar || 'https://cdn.vuetifyjs.com/images/avatars/avatar-1.png'"></v-img>
                </v-avatar>
                <div>
                  <p class="subtitle">{{ review.user.name }}</p>
                  <p class="text text-caption">{{ formatDate(review.created_at) }}</p>
                </div>
              </div>
              <v-rating :model-value="review.rating" readonly color="alert" density="compact" class="mb-2" half-increments></v-rating>
              <p class="text">"{{ review.comment }}"</p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <div v-else class="text-center py-16 text color-subtitle">Ainda não há avaliações. Seja o primeiro a deixar a sua!</div>
  </v-container>
</template>
