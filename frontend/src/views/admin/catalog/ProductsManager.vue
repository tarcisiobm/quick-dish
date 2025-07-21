<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';
import { useCatalogStore } from '@/stores/catalog';

interface Ingredient {
  id: number;
  name: string;
}

interface ProductIngredient {
    id: number;
    quantity: number | string;
    // O backend aninha os dados do pivô assim
    pivot?: {
        quantity: number | string;
    }
}

interface Product {
  id: number;
  name: string;
  description: string;
  price: string | number;
  promotional_price: string | number | null;
  category_id: number;
  ingredients: ProductIngredient[];
}

interface ProductForm {
  name: string;
  description: string;
  price: number | null;
  promotional_price: number | null;
  category_id: number | null;
  ingredient_ids: number[];
  ingredient_quantities: Record<number, number | null>;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const catalogStore = useCatalogStore();

const products = ref<Product[]>([]);
const ingredients = ref<Ingredient[]>([]);
const loading = ref(false);
const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<Product | null>(null);
const itemToDelete = ref<Product | null>(null);

const formDefault: ProductForm = {
  name: '', description: '', price: null, promotional_price: null,
  category_id: null, ingredient_ids: [], ingredient_quantities: {},
};
const form = ref<ProductForm>({ ...formDefault });

const formTitle = computed(() => (editedItem.value ? 'Editar Produto' : 'Novo Produto'));

const selectedIngredients = computed(() => {
    return ingredients.value.filter(ing => form.value.ingredient_ids.includes(ing.id));
});

const headers = [
  { title: 'Nome', key: 'name' },
  { title: 'Preço', key: 'price' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' },
] as const;

async function fetchProducts() {
  loading.value = true;
  try {
    const response = await api.get('/products');
    products.value = response.data.data;
  } catch (error) { errorStore.handle(error); }
  finally { loading.value = false; }
}

async function fetchIngredients() {
  try {
    const response = await api.get('/ingredients');
    ingredients.value = response.data.data;
  } catch (error) { errorStore.handle(error); }
}

function openNewDialog() {
  editedItem.value = null;
  form.value = { ...formDefault };
  dialog.value = true;
}

function openEditDialog(item: Product) {
  editedItem.value = { ...item };

  const quantities: Record<number, number | null> = {};
  item.ingredients.forEach(ing => {
      quantities[ing.id] = Number(ing.pivot?.quantity ?? 1);
  });

  form.value = {
    name: item.name,
    description: item.description,
    price: Number(item.price),
    promotional_price: item.promotional_price ? Number(item.promotional_price) : null,
    category_id: item.category_id,
    ingredient_ids: item.ingredients.map(ing => ing.id),
    ingredient_quantities: quantities,
  };
  dialog.value = true;
}

function openDeleteDialog(item: Product) {
    itemToDelete.value = item;
    deleteDialog.value = true;
}

function closeDialog() {
  dialog.value = false;
  nextTick(() => {
    editedItem.value = null;
    form.value = { ...formDefault };
  });
}

function closeDeleteDialog() {
    deleteDialog.value = false;
    nextTick(() => {
        itemToDelete.value = null;
    });
}

async function save() {
    try {
        const payload = {
            name: form.value.name,
            description: form.value.description,
            price: form.value.price,
            promotional_price: form.value.promotional_price,
            category_id: form.value.category_id,
            ingredients: form.value.ingredient_ids.map(id => ({
                id: id,
                quantity: form.value.ingredient_quantities[id] || 1
            }))
        };

        if (editedItem.value) {
            await api.put(`/products/${editedItem.value.id}`, payload);
        } else {
            await api.post('/products', payload);
        }

        snackbarStore.success('Produto salvo com sucesso!');
        fetchProducts();
        closeDialog();
    } catch(error) {
        errorStore.handle(error);
    }
}

async function deleteItemConfirm() {
    if (!itemToDelete.value) return;
    try {
        await api.delete(`/products/${itemToDelete.value.id}`);
        snackbarStore.success('Produto excluído com sucesso!');
        fetchProducts();
        closeDeleteDialog();
    } catch (error) {
        errorStore.handle(error);
    }
}

onMounted(() => {
  fetchProducts();
  catalogStore.fetchCategories();
  fetchIngredients();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Produtos</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Produto</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="products" :loading="loading" class="text">
         <template v-slot:item.price="{ item }">
          R$ {{ Number(item.price).toFixed(2) }}
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon icon="mdi-pencil" size="small" class="mr-2" @click="openEditDialog(item)"></v-icon>
            <v-icon icon="mdi-delete" size="small" @click="openDeleteDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="700px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text class="py-4">
          <v-text-field v-model="form.name" label="Nome do Produto" class="mb-4" />
          <v-select
            v-model="form.category_id"
            :items="catalogStore.categoryList"
            item-title="name"
            item-value="id"
            label="Categoria"
            class="mb-4"
          />
          <v-textarea v-model="form.description" label="Descrição" class="mb-4" />
          <v-row>
            <v-col cols="6"><v-text-field v-model="form.price" label="Preço" type="number" prefix="R$" /></v-col>
            <v-col cols="6"><v-text-field v-model="form.promotional_price" label="Preço Promocional (Opcional)" type="number" prefix="R$" /></v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>
          <p class="subtitle mb-2">Receita</p>

          <v-autocomplete
            v-model="form.ingredient_ids"
            :items="ingredients"
            item-title="name"
            item-value="id"
            label="Selecione os Ingredientes"
            multiple
            chips
            closable-chips
          />

          <v-list v-if="form.ingredient_ids.length > 0" lines="two" class="mt-4">
            <v-list-item
              v-for="ingredient in selectedIngredients"
              :key="ingredient.id"
              class="px-0"
            >
              <v-list-item-title class="text">{{ ingredient.name }}</v-list-item-title>
              <template #append>
                <v-text-field
                  v-model="form.ingredient_quantities[ingredient.id]"
                  label="Quantidade"
                  type="number"
                  density="compact"
                  style="width: 150px"
                  hide-details
                />
              </template>
            </v-list-item>
          </v-list>

        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="500px">
        <v-card>
            <v-card-title class="text-h5 subtitle">Confirmar Exclusão</v-card-title>
            <v-card-text class="text">Tem certeza que deseja excluir o produto "{{ itemToDelete?.name }}"?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="text" variant="text" @click="closeDeleteDialog">Cancelar</v-btn>
                <v-btn color="error" variant="elevated" @click="deleteItemConfirm">Excluir</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>

  </v-card>
</template>
