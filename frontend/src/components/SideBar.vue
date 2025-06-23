<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useSidebarStore } from '@/stores/sidebar';
import { useI18n } from 'vue-i18n';

interface MenuItemLink {
  title: string;
  icon: string;
  to: string;
  children?: never;
}

interface MenuItemGroup {
  title: string;
  icon: string;
  children: { title: string; to: string }[];
  to?: never;
}

type MenuItem = MenuItemLink | MenuItemGroup;

interface SearchResult {
  title: string;
  to: string;
  icon: string;
  parent?: string;
}

const sidebar = useSidebarStore();
const { t } = useI18n();

const searchValue = ref('');
const openMenu = ref<string[]>([]);
const lastOpenMenu = ref<string[]>([]);

const menuItems: readonly MenuItem[] = [
  {
    title: 'Menu',
    icon: 'mdi-clipboard-text-outline',
    children: [
      { title: 'Pizzas', to: '/menu/pizzas' },
      { title: 'Salads', to: '/menu/salads' }
    ]
  },
  {
    title: 'Reservations',
    icon: 'mdi-calendar-month-outline',
    to: '/reservations'
  },
  { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', to: '/dashboard' },
  { title: 'Notifications', icon: 'mdi-bell-outline', to: '/notifications' },
  { title: 'Settings', icon: 'mdi-cog-outline', to: '/settings' },
  { title: 'Help', icon: 'mdi-help-circle-outline', to: '/help' }
];

const searchResults = computed<SearchResult[]>(() => {
  if (!searchValue.value.trim()) return [];

  const search = searchValue.value.toLowerCase();
  return menuItems.flatMap((item) => {
    const results: SearchResult[] = [];

    if (item.to && item.title.toLowerCase().includes(search)) {
      results.push({ title: item.title, to: item.to, icon: item.icon });
    }

    if (item.children) {
      const parentMatches = item.title.toLowerCase().includes(search);
      results.push(
        ...item.children
          .filter((child) => parentMatches || child.title.toLowerCase().includes(search))
          .map((child) => ({
            title: child.title,
            to: child.to,
            parent: item.title,
            icon: item.icon
          }))
      );
    }

    return results;
  });
});

watch(
  () => sidebar.isExpanded,
  (isExpanded) => {
    if (isExpanded) {
      openMenu.value = lastOpenMenu.value;
      return;
    }

    lastOpenMenu.value = [...openMenu.value];
    openMenu.value = [];
    searchValue.value = '';
  }
);
</script>

<template>
  <div class="sidebar" :class="{ 'sidebar--expanded': sidebar.isExpanded, 'sidebar--pinned': sidebar.isPinned }" @mouseenter="sidebar.expandSidebar" @mouseleave="sidebar.collapseSidebar">
    <div class="sidebar-header">
      <v-text-field v-if="sidebar.isExpanded" v-model="searchValue" variant="outlined" prepend-inner-icon="mdi-magnify" :placeholder="t('fields.search')" density="compact" hide-details />
      <v-icon v-else>mdi-magnify</v-icon>
    </div>

    <div class="sidebar-menu d-flex flex-column" :class="{ 'justify-center': !searchValue.trim() }">
      <v-list v-model:opened="openMenu" nav density="compact" class="pa-0" style="background-color: transparent; overflow: hidden">
        <template v-if="searchValue.trim()">
          <v-list-subheader v-if="searchResults.length">{{ t('fields.searchResults') }}</v-list-subheader>
          <template v-if="searchResults.length">
            <v-list-item class="pa-0 menu-list-item" v-for="result in searchResults" :key="`${result.title}-${result.parent || ''}`" :to="result.to" :prepend-icon="result.icon" :title="result.title" :subtitle="result.parent" />
          </template>
          <v-list-item v-else>
            <v-list-item-title class="text-center color-text">{{ t('fields.noResults') }}</v-list-item-title>
          </v-list-item>
        </template>

        <template v-else>
          <template v-for="item in menuItems" :key="item.title">
            <v-list-group v-if="item.children" :value="item.title">
              <template #activator="{ props }">
                <v-list-item class="pa-0 menu-list-item" v-bind="props" :prepend-icon="item.icon" :title="item.title" />
              </template>
              <v-list-item v-for="sub in item.children" :key="sub.title" :to="sub.to" :title="sub.title" class="submenu-item menu-list-subitem" />
            </v-list-group>

            <v-list-item class="pa-0 menu-list-item" v-else :prepend-icon="item.icon" :title="item.title" :to="item.to" />
          </template>
        </template>
      </v-list>
    </div>

    <div class="sidebar-footer" :class="{ 'align-self-end': sidebar.isExpanded }">
      <v-btn color="text" :icon="sidebar.isPinned ? 'mdi-pin' : 'mdi-pin-outline'" variant="text" height="undefined" @click="sidebar.togglePin" />
    </div>
  </div>
</template>

<style scoped>
.sidebar {
  position: fixed;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  top: 56px;
  bottom: 0;
  width: 56px;
  padding: 0 16px;
  background-color: rgb(var(--v-theme-alt_background));
  transition: width 0.3s ease;
  z-index: 10;
  overflow: hidden;
}

.sidebar--expanded {
  width: 332px;
  padding-top: 7px;
  border-right: 1px solid rgba(var(--v-theme-border), 0.5);
}

.sidebar--pinned {
  border-right: none !important;
}

.sidebar-header {
  height: 60px;
  display: flex;
  align-items: center;
}

.sidebar-menu {
  flex: 1;
  overflow-y: auto;
}

.sidebar-footer {
  padding: 8px 0;
}

.submenu-item {
  padding-inline-start: 38px !important;
}

.menu-list-item {
  color: rgb(var(--v-theme-text));
  font-size: 16px !important;
}

.menu-list-item:deep(.v-list-item-title) {
  color: rgb(var(--v-theme-text));
  font-size: 16px;
  font-weight: 500;
}

.menu-list-item :deep(.v-list-item__prepend i) {
  color: rgb(var(--v-theme-text));
  opacity: 1 !important;
}

.menu-list-subitem {
  color: rgb(var(--v-theme-text));
  font-size: 13px !important;
}

.menu-list-subitem:deep(.v-list-item-title) {
  color: rgb(var(--v-theme-text));
  font-size: 14px;
  font-weight: 400;
  padding-left: 30px;
}
</style>
