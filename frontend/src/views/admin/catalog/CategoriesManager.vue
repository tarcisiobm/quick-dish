<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import { useCatalogStore, type Category } from '@/stores/catalog';
import { useSnackbarStore } from '@/stores/snackbar';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';

const catalogStore = useCatalogStore();
const snackbarStore = useSnackbarStore();
const errorStore = useErrorStore();

const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<Category | null>(null);
const itemToDelete = ref<Category | null>(null);
const form = ref({ name: '', description: '' });

const formTitle = computed(() => (editedItem.value ? 'Editar Categoria' : 'Nova Categoria'));

const headers = [
  { title: 'Nome', key: 'name' },
  { title: 'Descrição', key: 'description' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' },
] as const;

function openNewDialog() {
  editedItem.value = null;
  form.value = { name: '', description: '' };
  dialog.value = true;
}

function openEditDialog(item: Category) {
  editedItem.value = { ...item };
  form.value = {
    name: item.name,
    description: item.description ?? '',
  };
  dialog.value = true;
}

function openDeleteDialog(item: Category) {
  itemToDelete.value = item;
  deleteDialog.value = true;
}

async function save() {
  try {
    if (editedItem.value) {
      const response = await api.put(`/categories/${editedItem.value.id}`, form.value);
      snackbarStore.success(response.data.message);
    } else {
      await catalogStore.createCategory(form.value);
    }
    await catalogStore.fetchCategories();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function deleteItemConfirm() {
  if (!itemToDelete.value) return;
  try {
    const response = await api.delete(`/categories/${itemToDelete.value.id}`);
    snackbarStore.success(response.data.message);
    await catalogStore.fetchCategories();
    closeDeleteDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialog() {
  dialog.value = false;
  nextTick(() => {
    editedItem.value = null;
    form.value = { name: '', description: '' };
  });
}

function closeDeleteDialog() {
  deleteDialog.value = false;
  nextTick(() => {
    itemToDelete.value = null;
  });
}

onMounted(() => {
  catalogStore.fetchCategories();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Categorias</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Nova Categoria</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table :headers="headers" :items="catalogStore.categoryList" class="text">
        <template v-slot:item.actions="{ item }">
          <v-icon icon="mdi-pencil" size="small" class="mr-2" @click="openEditDialog(item)"></v-icon>
          <v-icon icon="mdi-delete" size="small" @click="openDeleteDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="500px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.name" label="Nome da Categoria"></v-text-field>
          <v-text-field v-model="form.description" label="Descrição (Opcional)"></v-text-field>
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
        <v-card-text class="text">Tem certeza que deseja excluir a categoria "{{ itemToDelete?.name }}"?</v-card-text>
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
