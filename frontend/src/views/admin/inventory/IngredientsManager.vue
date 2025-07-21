<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';
import { useInventoryStore, type UnitMeasure, type Supplier } from '@/stores/inventory';

interface Ingredient {
  id: number; name: string; unit_price: number | string; quantity: number | string;
  min_quantity: number | string | null; supplier_id: number; unit_measure_id: number;
  is_additional: boolean; unit_measure: UnitMeasure; supplier: Supplier;
}

interface IngredientForm {
  name: string; unit_price: number | null; quantity: number | null;
  min_quantity: number | null; supplier_id: number | null;
  unit_measure_id: number | null; is_additional: boolean;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const inventoryStore = useInventoryStore();

const ingredients = ref<Ingredient[]>([]);
const loading = ref(true);
const dialog = ref(false);
const deleteDialog = ref(false);
const editedItem = ref<Ingredient | null>(null);
const itemToDelete = ref<Ingredient | null>(null);

const formDefault: IngredientForm = {
  name: '', unit_price: null, quantity: null, min_quantity: null,
  supplier_id: null, unit_measure_id: null, is_additional: false,
};
const form = ref<IngredientForm>({ ...formDefault });

const supplierDialog = ref(false);
const newSupplier = ref<Omit<Supplier, 'id'>>({ name: '' });
const unitMeasureDialog = ref(false);
const newUnitMeasure = ref<Omit<UnitMeasure, 'id'>>({ name: '', abbreviation: '' });

const formTitle = computed(() => (editedItem.value ? 'Editar Ingrediente' : 'Novo Ingrediente'));

const headers = [
  { title: 'Nome', key: 'name' }, { title: 'Fornecedor', key: 'supplier.name' },
  { title: 'Estoque', key: 'quantity' },
  { title: 'Preço Unitário', key: 'unit_price' }, { title: 'Ações', key: 'actions', sortable: false, align: 'end' },
] as const;

async function fetchIngredients() {
  loading.value = true;
  try {
    const response = await api.get('/ingredients');
    ingredients.value = response.data.data;
  } catch (error) { errorStore.handle(error); }
  finally { loading.value = false; }
}

function openNewDialog() {
  editedItem.value = null;
  form.value = { ...formDefault };
  dialog.value = true;
}

function openEditDialog(item: Ingredient) {
  editedItem.value = { ...item };
  form.value = {
    name: item.name,
    unit_price: Number(item.unit_price),
    quantity: Number(item.quantity),
    min_quantity: item.min_quantity ? Number(item.min_quantity) : null,
    supplier_id: item.supplier_id,
    unit_measure_id: item.unit_measure_id,
    is_additional: item.is_additional,
  };
  dialog.value = true;
}

function openDeleteDialog(item: Ingredient) {
  itemToDelete.value = item;
  deleteDialog.value = true;
}

async function save() {
  try {
    if (editedItem.value) {
      const response = await api.put(`/ingredients/${editedItem.value.id}`, form.value);
      snackbarStore.success(response.data.message);
    } else {
      const response = await api.post('/ingredients', form.value);
      snackbarStore.success(response.data.message);
    }
    fetchIngredients();
    closeDialog();
  } catch (error) { errorStore.handle(error); }
}

async function deleteItemConfirm() {
  if (!itemToDelete.value) return;
  try {
    const response = await api.delete(`/ingredients/${itemToDelete.value.id}`);
    snackbarStore.success(response.data.message);
    fetchIngredients();
    closeDeleteDialog();
  } catch (error) { errorStore.handle(error); }
}

async function quickSaveSupplier() {
  const createdSupplier = await inventoryStore.createSupplier(newSupplier.value);
  if (createdSupplier) {
    form.value.supplier_id = createdSupplier.id;
    newSupplier.value = { name: '' };
    supplierDialog.value = false;
  }
}

async function quickSaveUnitMeasure() {
  const createdUnitMeasure = await inventoryStore.createUnitMeasure(newUnitMeasure.value);
  if (createdUnitMeasure) {
    form.value.unit_measure_id = createdUnitMeasure.id;
    newUnitMeasure.value = { name: '', abbreviation: '' };
    unitMeasureDialog.value = false;
  }
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

onMounted(() => {
  fetchIngredients();
  inventoryStore.fetchSuppliers();
  inventoryStore.fetchUnitMeasures();
});
</script>

<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Ingredientes</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Ingrediente</v-btn>
    </v-card-title>

    <v-card-text>
      <v-data-table
        :headers="headers" :items="ingredients" :loading="loading"
        class="text" no-data-text="Nenhum ingrediente encontrado."
      >
        <template v-slot:item.quantity="{ item }">{{ item.quantity }} {{ item.unit_measure?.abbreviation }}</template>
        <template v-slot:item.unit_price="{ item }">R$ {{ Number(item.unit_price).toFixed(2) }}</template>
        <template v-slot:item.actions="{ item }">
          <v-icon icon="mdi-pencil" size="small" class="mr-2" @click="openEditDialog(item)"></v-icon>
          <v-icon icon="mdi-delete" size="small" @click="openDeleteDialog(item)"></v-icon>
        </template>
      </v-data-table>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="600px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.name" label="Nome do Ingrediente" class="mb-4"/>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.quantity" label="Qtd. em Estoque" type="number"/></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="form.min_quantity" label="Estoque Mínimo" type="number"/></v-col>
          </v-row>
          <v-text-field v-model="form.unit_price" label="Preço por Unidade/Kg" type="number" prefix="R$" class="my-4"/>
          <v-select
            v-model="form.supplier_id" :items="inventoryStore.supplierList" item-title="name" item-value="id"
            label="Fornecedor" class="mb-4"
          >
            <template #append><v-btn icon="mdi-plus" size="small" variant="tonal" @click="supplierDialog = true"/></template>
          </v-select>
          <v-select
            v-model="form.unit_measure_id" :items="inventoryStore.unitMeasureList" item-title="name" item-value="id"
            label="Unidade de Medida"
          >
            <template #append><v-btn icon="mdi-plus" size="small" variant="tonal" @click="unitMeasureDialog = true"/></template>
          </v-select>
          <v-checkbox v-model="form.is_additional" label="É um item adicional?"/>
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="supplierDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title class="subtitle">Novo Fornecedor</v-card-title>
        <v-card-text><v-text-field v-model="newSupplier.name" label="Nome do Fornecedor"/></v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn color="text" variant="text" @click="supplierDialog = false">Cancelar</v-btn>
          <v-btn @click="quickSaveSupplier">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="unitMeasureDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title class="subtitle">Nova Unidade de Medida</v-card-title>
        <v-card-text>
          <v-text-field v-model="newUnitMeasure.name" label="Nome (ex: Quilograma)"/>
          <v-text-field v-model="newUnitMeasure.abbreviation" label="Abreviação (ex: kg)"/>
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn color="text" variant="text" @click="unitMeasureDialog = false">Cancelar</v-btn>
          <v-btn @click="quickSaveUnitMeasure">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

     <v-dialog v-model="deleteDialog" max-width="500px">
        <v-card>
            <v-card-title class="text-h5 subtitle">Confirmar Exclusão</v-card-title>
            <v-card-text class="text">Tem certeza que deseja excluir o ingrediente "{{ itemToDelete?.name }}"?</v-card-text>
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
