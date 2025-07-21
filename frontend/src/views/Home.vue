<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCatalogStore } from '@/stores/catalog';

const router = useRouter();
const catalogStore = useCatalogStore();

onMounted(() => {
  if (catalogStore.categoryList.length === 0) {
    catalogStore.fetchCategories();
  }
});
</script>

<template>
  <div>
    <v-sheet class="d-flex align-center" height="calc(100vh - 62px)">
      <v-container>
        <v-row align="center" justify="center" class="text-center">
          <v-col cols="12" md="10" lg="8">
            <h1 class="title text-h2 mb-4" style="line-height: 1.2">Sabor que Conecta Pessoas</h1>
            <p class="text text-h6 color-subtitle font-weight-regular mb-8">Ingredientes frescos, receitas autênticas e um ambiente acolhedor. A experiência que você merece.</p>
            <div class="d-flex justify-center ga-4">
              <v-btn size="x-large" @click="router.push('/menu')"> Ver Cardápio </v-btn>
              <v-btn size="x-large" variant="outlined" @click="router.push('/reservations')"> Fazer Reserva </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>

    <v-container class="py-16">
      <v-row justify="center">
        <v-col cols="12" class="text-center">
          <h2 class="title text-h4 mb-2">Explore Nossas Categorias</h2>
          <p class="text color-subtitle">Do clássico ao contemporâneo, uma viagem de sabores.</p>
        </v-col>
      </v-row>
      <v-row class="mt-8">
        <v-col v-for="category in catalogStore.categoryList.slice(0, 4)" :key="category.id" cols="12" sm="6" md="3">
          <v-card :to="`/menu/category/${category.id}`" hover>
            <v-img height="250" :src="category.image_path || undefined" cover class="align-end text-white">
              <template #error>
                <v-sheet color="input_background" class="d-flex align-center justify-center fill-height">
                  <v-icon color="grey-lighten-1" icon="mdi-food-variant" size="80"></v-icon>
                </v-sheet>
              </template>
              <v-card-title class="subtitle" style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent)">
                {{ category.name }}
              </v-card-title>
            </v-img>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-sheet color="alt_background" class="py-16">
      <v-container>
        <v-row align="center" justify="center" class="text-center">
          <v-col cols="12" md="8">
            <h2 class="title text-h4 mb-4">Pronto para uma <br />Experiência Inesquecível?</h2>
            <p class="text text-body-1 color-subtitle mb-8">Seja para um jantar especial ou um encontro casual, garantimos o ambiente perfeito.</p>
            <v-btn size="x-large" color="primary" @click="router.push('/reservations')" prepend-icon="mdi-calendar-check"> Reservar sua Mesa </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>
  </div>
</template>
