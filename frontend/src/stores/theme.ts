// stores/theme.ts
import { defineStore } from 'pinia';
import { ref, watch } from 'vue';
import { useTheme } from 'vuetify';

export const useThemeStore = defineStore('theme', () => {
  const vuetifyTheme = useTheme();

  const isDark = ref<boolean>(localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches));

  const toggleTheme = () => {
    isDark.value = !isDark.value;
  };

  watch(
    isDark,
    (dark) => {
      localStorage.setItem('theme', dark ? 'dark' : 'light');
      vuetifyTheme.global.name.value = dark ? 'dark' : 'light';
    },
    { immediate: true }
  );

  return { isDark, toggleTheme };
});
