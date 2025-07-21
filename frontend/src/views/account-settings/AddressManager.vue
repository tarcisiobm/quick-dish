<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';
import { useAuthStore } from '@/stores/auth';

interface Address {
  id: number;
  street: string;
  number: string;
  complement: string | null;
  neighborhood: string;
  city: string;
  state: string;
  zipcode: string;
}

interface AddressForm {
  street: string;
  number: string;
  complement: string | null;
  neighborhood: string;
  city: string;
  state: string;
  zipcode: string;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();
const authStore = useAuthStore();

const addresses = ref<Address[]>([]);
const loading = ref(true);
const dialog = ref(false);
const editedItem = ref<Address | null>(null);

const formDefault: AddressForm = { street: '', number: '', complement: '', neighborhood: '', city: '', state: '', zipcode: '' };
const form = ref<AddressForm>({ ...formDefault });

const formTitle = computed(() => (editedItem.value ? 'Editar Endereço' : 'Novo Endereço'));

async function fetchAddresses() {
  loading.value = true;
  try {
    const response = await api.get('/addresses');
    addresses.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

function openNewDialog() {
  editedItem.value = null;
  form.value = { ...formDefault };
  dialog.value = true;
}

function openEditDialog(item: Address) {
  editedItem.value = { ...item };
  form.value = {
    ...item,
    complement: item.complement ?? ''
  };
  dialog.value = true;
}

async function save() {
  try {
    const payload = { ...form.value, user_id: authStore.user?.id };
    let response;
    if (editedItem.value) {
      response = await api.put(`/addresses/${editedItem.value.id}`, payload);
    } else {
      response = await api.post('/addresses', payload);
    }
    snackbarStore.success(response.data.message);
    fetchAddresses();
    closeDialog();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function deleteAddress(id: number) {
  if (confirm('Tem certeza que deseja excluir este endereço?')) {
    try {
      const response = await api.delete(`/addresses/${id}`);
      snackbarStore.success(response.data.message);
      fetchAddresses();
    } catch (error) {
      errorStore.handle(error);
    }
  }
}

function closeDialog() {
  dialog.value = false;
}

onMounted(fetchAddresses);
</script>

<template>
  <v-card variant="outlined">
    <v-card-title class="d-flex justify-space-between align-center">
      <span class="subtitle">Meus Endereços</span>
      <v-btn prepend-icon="mdi-plus" @click="openNewDialog">Novo Endereço</v-btn>
    </v-card-title>
    <v-card-text>
      <v-row v-if="!loading">
        <v-col v-for="address in addresses" :key="address.id" cols="12" md="6">
          <v-card variant="tonal">
            <v-card-text>
              <p class="text">
                <strong>{{ address.street }}, {{ address.number }}</strong>
              </p>
              <p class="text">{{ address.neighborhood }} - {{ address.city }}/{{ address.state }}</p>
              <p class="text">{{ address.zipcode }}</p>
              <p v-if="address.complement" class="text">Complemento: {{ address.complement }}</p>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn icon="mdi-pencil" variant="text" size="small" @click="openEditDialog(address)"></v-btn>
              <v-btn icon="mdi-delete" variant="text" size="small" color="error" @click="deleteAddress(address.id)"></v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col v-if="addresses.length === 0" class="text-center text color-subtitle py-8"> Você ainda não cadastrou nenhum endereço. </v-col>
      </v-row>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="700px" persistent>
      <v-card>
        <v-card-title class="subtitle">{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="8"><v-text-field v-model="form.street" label="Rua"></v-text-field></v-col>
            <v-col cols="12" md="4"><v-text-field v-model="form.number" label="Número"></v-text-field></v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.complement" label="Complemento (Opcional)"></v-text-field></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="form.neighborhood" label="Bairro"></v-text-field></v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="form.city" label="Cidade"></v-text-field></v-col>
            <v-col cols="12" md="3"><v-text-field v-model="form.state" label="Estado (UF)"></v-text-field></v-col>
            <v-col cols="12" md="3"><v-text-field v-model="form.zipcode" label="CEP"></v-text-field></v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn @click="save">Salvar Endereço</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>
