<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { RouterLink, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useSidebarStore } from '@/stores/sidebar';
import { ref, onMounted, onUnmounted } from 'vue';
import { PhBasket, PhUserCircle, PhPencilSimple, PhSignOut, PhCaretDown } from '@phosphor-icons/vue';
import { useCartStore } from '@/stores/cart';

const router = useRouter();
const auth = useAuthStore();
const sidebar = useSidebarStore();
const cartStore = useCartStore();
const { t } = useI18n();

const isScrolled = ref(false);

function handleScroll() {
  isScrolled.value = window.scrollY > 0;
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <nav :class="['superior-nav-bar d-flex justify-space-between px-2 w-100', { scrolled: isScrolled, 'sidebar-expanded': sidebar.isExpanded }]">
    <div @click="router.push('/')" class="d-flex align-center ga-3 cursor-pointer">
      <img width="45" src="@/assets/logo.svg" />
      <h1 class="font-22 bold">Quick<span class="font-22 bold color-primary">Dish</span></h1>
    </div>

    <div class="d-flex justify-center align-center ga-4">
      <RouterLink to="/" class="nav-link color-text">Home</RouterLink>
      <RouterLink to="/menu" class="nav-link color-text">Menu</RouterLink>
      <RouterLink to="/contact" class="nav-link color-text">Contact</RouterLink>
      <RouterLink to="/reviews" class="nav-link color-text">Reviews</RouterLink>
    </div>

    <div class="d-flex justify-center align-center ga-4">
      <v-btn height="undefined" icon variant="outlined" @click="cartStore.toggleDrawer">
        <v-badge :content="cartStore.itemCount" :model-value="cartStore.itemCount > 0" color="primary">
          <PhBasket size="22" class="color-text_low_opacity" />
        </v-badge>
      </v-btn>

      <div v-if="!auth.isAuthenticated" class="d-flex justify-center align-center ga-4">
        <v-btn class="btn-md" color="text" variant="text" @click="router.push('/login')">
          <template v-slot:prepend class="d-flex align-center"> <PhUserCircle size="22" class="color-text_low_opacity mr-2" /> </template>
          Login
        </v-btn>
        <v-btn class="btn-md" @click="router.push('/signup')">SignUp</v-btn>
      </div>

      <div v-else width="36" class="user-card d-flex justify-center align-center">
        <img :src="auth.user?.avatar ?? require('@/assets/user-default.png')" class="user-avatar-image"></img>
        <p class="font-14 color-text">{{ auth.user?.name }}</p>
        <v-btn color="text" icon variant="text" height="undefined" density="compact">
          <PhCaretDown weight="fill" size="12" class="color-text_low_opacity" />
        </v-btn>
        <v-menu activator="parent">
          <v-list class="user-options-menu text">
            <v-list-item to="/account-settings" base-color="text" :prepend-icon="PhPencilSimple">
              <v-list-item-title>{{ t('appBar.editProfile') }}</v-list-item-title>
            </v-list-item>
            <v-list-item @click="auth.logout" base-color="error_dense" :prepend-icon="PhSignOut">
              <v-list-item-title class="color-error_dense">{{ t('appBar.logout') }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </div>
  </nav>
</template>

<style lang="scss" scoped>
.superior-nav-bar {
  position: fixed;
  z-index: 10;
  background-color: rgb(var(--v-theme-alt_background));
  height: 62px;
}

.superior-nav-bar::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 56px;
  right: 0;
  border-bottom: 0.5px solid transparent;
  pointer-events: none;
}

.superior-nav-bar.sidebar-expanded::after {
  left: 332px;
}

.superior-nav-bar.scrolled::after {
  border-color: rgb(var(--v-theme-border));
}

.user-avatar-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  overflow: hidden;
  display: block;
}


.nav-link {
  text-decoration: none;
  text-transform: uppercase;
}

.user-card {
  max-width: 234px;
  height: 46px;
  gap: 10px;
  padding: 5px 15px;

  p {
    max-width: 124px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
.user-options-menu {
  width: 220px;
  background-color: rgb(var(--v-theme-alt_background)) !important;
}
</style>
