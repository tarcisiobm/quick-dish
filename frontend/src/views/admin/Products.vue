<script setup lang="ts">
import { ref } from 'vue';
import ProductsManager from '@/views/admin/catalog/ProductsManager.vue';
import CategoriesManager from '@/views/admin/catalog/CategoriesManager.vue';

const tab = ref('products');
</script>
<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gestão de Cardápio</h1>
    <p class="text text-body-1 color-subtitle mb-6">Administre os produtos e suas respectivas categorias.</p>

    <v-tabs v-model="tab" color="primary" grow>
      <v-tab value="products" class="text">Produtos</v-tab>
      <v-tab value="categories" class="text">Categorias</v-tab>
    </v-tabs>

    <v-window v-model="tab" class="mt-5">
      <v-window-item value="products">
        <ProductsManager />
      </v-window-item>
      <v-window-item value="categories">
        <CategoriesManager />
      </v-window-item>
    </v-window>
  </v-container>
</template>
