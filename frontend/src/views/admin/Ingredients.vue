<script setup lang="ts">
import { ref } from 'vue';
import IngredientsManager from '@/views/admin/inventory/IngredientsManager.vue';
import SuppliersManager from '@/views/admin/inventory/SuppliersManager.vue';
import UnitMeasuresManager from '@/views/admin/inventory/UnitMeasuresManager.vue';

const tab = ref('ingredients');
</script>

<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gestão de Estoque</h1>
    <p class="text text-body-1 color-subtitle mb-6">Controle seus insumos, fornecedores e as unidades de medida utilizadas.</p>

    <v-tabs v-model="tab" color="primary" grow>
      <v-tab value="ingredients" class="text">Ingredientes</v-tab>
      <v-tab value="suppliers" class="text">Fornecedores</v-tab>
      <v-tab value="unit-measures" class="text">Unidades de Medida</v-tab>
    </v-tabs>

    <v-window v-model="tab" class="mt-5">
      <v-window-item value="ingredients">
        <IngredientsManager />
      </v-window-item>

      <v-window-item value="suppliers">
        <SuppliersManager />
      </v-window-item>

      <v-window-item value="unit-measures">
        <UnitMeasuresManager />
      </v-window-item>
    </v-window>
  </v-container>
</template>
