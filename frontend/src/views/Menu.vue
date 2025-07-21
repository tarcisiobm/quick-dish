<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useCatalogStore, type Category } from '@/stores/catalog';
import { useCartStore } from '@/stores/cart';
const cartStore = useCartStore();

interface Product {
  id: number;
  name: string;
  description: string;
  price: string;
  image_path: string | null;
  category_id: number;
}

interface MenuSection {
  id: number;
  name: string;
  products: Product[];
}

const props = defineProps<{
  id?: string;
}>();

const errorStore = useErrorStore();
const catalogStore = useCatalogStore();

const allProducts = ref<Product[]>([]);
const loading = ref(true);
const searchQuery = ref('');

async function fetchAllProducts() {
  loading.value = true;
  try {
    const response = await api.get('/products');
    allProducts.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

const menuSections = computed<MenuSection[]>(() => {
  const filteredProducts = searchQuery.value
    ? allProducts.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
    : allProducts.value;

  const sortedCategories = [...catalogStore.categoryList].sort((a, b) => (a.display_order || 0) - (b.display_order || 0));

  let categoriesToDisplay: Category[] = sortedCategories;

  if (props.id) {
    categoriesToDisplay = sortedCategories.filter(c => c.id === Number(props.id));
  }

  return categoriesToDisplay.map(category => ({
    id: category.id,
    name: category.name,
    products: filteredProducts.filter(product => product.category_id === category.id)
  })).filter(section => section.products.length > 0);
});

onMounted(() => {
  catalogStore.fetchCategories();
  fetchAllProducts();
});
</script>

<template>
  <v-container fluid>
    <div class="d-flex flex-wrap justify-space-between align-center mb-6 ga-4">
      <div>
        <h1 class="title text-h4">Nosso Cardápio</h1>
        <p class="text color-subtitle">Confira nossas delícias e faça seu pedido.</p>
      </div>
      <div style="max-width: 400px; flex-grow: 1;">
        <v-text-field
          v-model="searchQuery"
          label="Buscar por nome do produto..."
          prepend-inner-icon="mdi-magnify"
          hide-details
          variant="outlined"
          density="compact"
          clearable
        />
      </div>
    </div>

    <div v-if="loading">
      <v-row>
        <v-col v-for="n in 8" :key="n" cols="12" sm="6" md="4" lg="3">
          <v-skeleton-loader type="card-avatar, article"></v-skeleton-loader>
        </v-col>
      </v-row>
    </div>

    <div v-else-if="menuSections.length > 0">
      <section
        v-for="section in menuSections"
        :key="section.id"
        class="mb-8"
      >
        <h2 class="title text-h5 mb-4">{{ section.name }}</h2>
        <v-row>
          <v-col
            v-for="product in section.products"
            :key="product.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card hover class="fill-height d-flex flex-column">
              <v-img
                height="200"
                :src="product.image_path || undefined"
                cover
                class="bg-grey-lighten-2"
              >
                <template #error>
                  <v-sheet
                    color="input_background"
                    class="d-flex align-center justify-center fill-height"
                  >
                    <v-icon
                      color="grey-lighten-1"
                      icon="mdi-food-off-outline"
                      size="64"
                    ></v-icon>
                  </v-sheet>
                </template>
              </v-img>
              <v-card-title class="subtitle">{{ product.name }}</v-card-title>
              <v-card-text class="text flex-grow-1">{{ product.description }}</v-card-text>
              <v-card-actions class="px-4 pb-4">
                <span class="title">R$ {{ Number(product.price).toFixed(2) }}</span>
                <v-spacer />
                <v-btn @click="cartStore.addItem(product)">Adicionar</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </section>
    </div>

    <div v-else class="text-center py-16">
        <p class="text text-h6 color-subtitle">Nenhum produto encontrado para sua busca.</p>
    </div>

  </v-container>
</template>
