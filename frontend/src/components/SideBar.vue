<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useSidebarStore } from '@/stores/sidebar';
import { useI18n } from 'vue-i18n';
import { PhClipboardText, PhCalendarDots, PhLayout, PhBell, PhGear, PhQuestion, PhSidebarSimple, PhMoon, PhSun } from '@phosphor-icons/vue';
import { useThemeStore } from '@/stores/theme';
type PhosphorIcon = typeof PhClipboardText;

const sidebar = useSidebarStore();
const theme = useThemeStore();
const { t } = useI18n();

const searchValue = ref('');
const openMenu = ref<string[]>([]);
const lastOpenMenu = ref<string[]>([]);

interface MenuItemLink {
  title: string;
  icon: PhosphorIcon | string;
  to?: string;
  action?: () => void;
  children?: never;
}

interface MenuItemGroup {
  title: string;
  icon: PhosphorIcon | string;
  children: { title: string; to: string }[];
  to?: never;
  action?: never;
}

type MenuItem = MenuItemLink | MenuItemGroup;

interface SearchResult {
  title: string;
  to?: string;
  action?: () => void;
  icon: PhosphorIcon | string;
  parent?: string;
}

const menuItems = computed<MenuItem[]>(() => [
  {
    title: 'Menu',
    icon: PhClipboardText,
    children: [
      { title: 'Pizzas', to: '/menu/pizzas' },
      { title: 'Salads', to: '/menu/salads' }
    ]
  },
  {
    title: 'Reservations',
    icon: PhCalendarDots,
    to: '/reservations'
  },
  { title: 'Dashboard', icon: PhLayout, to: '/dashboard' },
  { title: 'Notifications', icon: PhBell, to: '/notifications' },
  { title: 'Settings', icon: PhGear, to: '/settings' },
  { title: 'Help', icon: PhQuestion, to: '/help' },
  {
    title: theme.isDark ? t('theme.enableLightMode') : t('theme.enableDarkMode'),
    icon: theme.isDark ? PhSun : PhMoon,
    action: theme.toggleTheme
  }
]);

const searchResults = computed<SearchResult[]>(() => {
  if (!searchValue.value.trim()) return [];

  const search = searchValue.value.toLowerCase();
  return menuItems.value.flatMap((item) => {
    const results: SearchResult[] = [];

    if ((item.to || item.action) && item.title.toLowerCase().includes(search)) {
      results.push({
        title: item.title,
        to: item.to,
        action: item.action,
        icon: item.icon
      });
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
      <v-text-field v-if="sidebar.isExpanded" v-model="searchValue" color="text" variant="outlined" prepend-inner-icon="mdi-magnify" :placeholder="t('fields.search')" density="compact" hide-details />
      <v-icon v-else class="search-icon">mdi-magnify</v-icon>
    </div>

    <div class="sidebar-menu d-flex flex-column" :class="{ 'justify-center': !searchValue.trim() }">
      <v-list v-model:opened="openMenu" nav density="compact" class="pa-0" style="background-color: transparent; overflow: hidden">
        <template v-if="searchValue.trim()">
          <v-list-subheader v-if="searchResults.length" class="color-text">{{ t('fields.searchResults') }}</v-list-subheader>
          <template v-if="searchResults.length">
            <v-list-item class="pa-0 menu-list-item" v-for="result in searchResults" :key="`${result.title}-${result.parent || ''}`" :to="result.to" :title="result.title" :subtitle="result.parent" :prepend-icon="result.icon" @click="result.action" />
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

            <v-list-item class="pa-0 menu-list-item" v-else :prepend-icon="item.icon" :title="item.title" :to="item.to" @click="item.action" />
          </template>
        </template>
      </v-list>
    </div>

    <div class="sidebar-footer" :class="{ 'align-self-end': sidebar.isExpanded }">
      <v-btn color="text" :icon="PhSidebarSimple" variant="text" height="undefined" @click="sidebar.togglePin">
        <PhSidebarSimple size="24" class="color-text" :weight="sidebar.isPinned ? 'fill' : 'regular'" />
      </v-btn>
    </div>
  </div>
</template>

<style scoped>
.sidebar {
  position: fixed;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  top: 62px;
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

.search-icon {
  background-color: rgb(var(--v-theme-input_background));
  border-radius: 100%;
  padding: 16px;
  border: 1.8px solid rgb(var(--v-theme-border));
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
