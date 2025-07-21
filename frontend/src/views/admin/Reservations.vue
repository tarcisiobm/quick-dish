<script setup lang="ts">
import { ref } from 'vue';
import ReservationsManager from '@/views/admin/reservation/ReservationsManager.vue';
import TablesManager from '@/views/admin/reservation/TablesManager.vue';

const tab = ref('reservations');
</script>

<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gestão de Reservas e Mesas</h1>
    <p class="text text-body-1 color-subtitle mb-6">Administre as reservas agendadas e a configuração das mesas do seu restaurante.</p>

    <v-tabs v-model="tab" color="primary" grow>
      <v-tab value="reservations" class="text">Reservas</v-tab>
      <v-tab value="tables" class="text">Mesas</v-tab>
    </v-tabs>

    <v-window v-model="tab" class="mt-5">
      <v-window-item value="reservations">
        <ReservationsManager />
      </v-window-item>
      <v-window-item value="tables">
        <TablesManager />
      </v-window-item>
    </v-window>
  </v-container>
</template>
