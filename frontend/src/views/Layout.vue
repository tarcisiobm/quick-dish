<script setup lang="ts">
import AppBar from '@/components/AppBar.vue';
import SideBar from '@/components/SideBar.vue';
import { useSidebarStore } from '@/stores/sidebar';
const sidebarStore = useSidebarStore();
import CartDrawer from '@/components/CartDrawer.vue';
</script>

<template>
  <v-app>
    <CartDrawer />
    <AppBar />
    <v-main :class="sidebarStore.isPinned ? 'main-view pined' : 'main-view default'">
      <RouterView />
    </v-main>
    <SideBar />
  </v-app>
</template>

<style scoped lang="scss">
.main-view {
  margin-top: 62px;
  border-top-left-radius: 4px;
  border-left: 0.5px solid rgb(var(--v-theme-window_border));
  border-top: 0.5px solid rgb(var(--v-theme-window_border));
}
.default {
  margin-left: 56px;
}

.pined {
  margin-left: 332px;
}
</style>
