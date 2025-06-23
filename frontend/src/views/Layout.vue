<script setup lang="ts">
import AppBar from '@/components/AppBar.vue'
import SideBar from '@/components/SideBar.vue'
import { useSidebarStore } from '@/stores/sidebar'
const sidebarStore = useSidebarStore()
</script>

<template>
  <v-app>
    <AppBar />
    <v-main :class="sidebarStore.isPinned ? 'main-view pined' : 'main-view default'">
      <RouterView />
    </v-main>
    <SideBar />
  </v-app>
</template>

<style scoped lang="scss">

.main-view{
  border-top-left-radius: 4px;
  border: 0.5px solid rgb(var(--v-theme-border));

}
.default{
  margin-left: 56px;
}

.pined{
  margin-left: 332px;
}
</style>
