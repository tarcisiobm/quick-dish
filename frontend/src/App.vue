<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <v-app>
    <v-app-bar app>
      <div class="wrapper">
        <nav class="d-flex align-baseline">
          <v-btn variant="text" to="/" class="mr-2">Home</v-btn>
          <v-btn variant="text" to="/login">Login</v-btn>
        </nav>
      </div>
    </v-app-bar>

    <v-main>
      <v-container>
        <RouterView />
      </v-container>
    </v-main>
  </v-app>
</template>

<style lang="scss" scoped>
</style>
