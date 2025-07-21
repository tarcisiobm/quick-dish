<script setup lang="ts">
import ProfileManager from '@/views/account-settings/ProfileManager.vue';
import AddressManager from '@/views/account-settings/AddressManager.vue';
</script>

<template>
  <v-container>
    <h1 class="title text-h4 mt-4 mb-8">Configurações da Conta</h1>

    <ProfileManager />

    <v-divider class="my-8"></v-divider>

    <AddressManager />
  </v-container>
</template>
