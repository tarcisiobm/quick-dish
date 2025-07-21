<script setup lang="ts">
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import api from '@/plugins/axios';
import { useErrorStore } from '@/stores/error';
import { useSnackbarStore } from '@/stores/snackbar';

interface Employee {
  id: number;
  job_title: string;
  salary: number | string;
  hire_date: string;
  work_schedule: string | null;
}

interface User {
  id: number;
  name: string;
  email: string;
  status: boolean;
  is_employee: boolean;
  employee: Employee | null;
}

interface UserForm {
  name: string;
  email: string;
  status: boolean;
}

interface EmployeeForm {
  user_id: number | null;
  job_title: string;
  salary: number | null;
  hire_date: string;
  work_schedule: string | null;
}

const errorStore = useErrorStore();
const snackbarStore = useSnackbarStore();

const users = ref<User[]>([]);
const loading = ref(false);
const filter = ref<'all' | 'employees' | 'customers'>('all');

const editUserDialog = ref(false);
const promoteEmployeeDialog = ref(false);
const demoteDialog = ref(false);
const editedUser = ref<User | null>(null);

const userFormDefault: UserForm = { name: '', email: '', status: true };
const userForm = ref<UserForm>({ ...userFormDefault });

const employeeFormDefault: EmployeeForm = { user_id: null, job_title: '', salary: null, hire_date: '', work_schedule: '' };
const employeeForm = ref<EmployeeForm>({ ...employeeFormDefault });

const headers = [
  { title: 'Nome', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Cargo', key: 'role' },
  { title: 'Status', key: 'status', align: 'center' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' }
] as const;

async function fetchUsers() {
  loading.value = true;
  try {
    const params: { is_employee?: boolean } = {};
    if (filter.value === 'employees') params.is_employee = true;
    if (filter.value === 'customers') params.is_employee = false;

    const response = await api.get('/users', { params });
    users.value = response.data.data;
  } catch (error) {
    errorStore.handle(error);
  } finally {
    loading.value = false;
  }
}

watch(filter, fetchUsers);

function openEditUserDialog(user: User) {
  editedUser.value = { ...user };
  userForm.value = { name: user.name, email: user.email, status: !!user.status };
  editUserDialog.value = true;
}

function openPromoteEmployeeDialog(user: User) {
  editedUser.value = { ...user };
  employeeForm.value = {
    user_id: user.id,
    job_title: user.employee?.job_title || '',
    salary: user.employee?.salary ? Number(user.employee.salary) : null,
    hire_date: user.employee?.hire_date || new Date().toISOString().split('T')[0],
    work_schedule: user.employee?.work_schedule || ''
  };
  promoteEmployeeDialog.value = true;
}

async function saveUser() {
  if (!editedUser.value) return;
  try {
    const response = await api.put(`/users/${editedUser.value.id}`, userForm.value);
    snackbarStore.success(response.data.message);
    fetchUsers();
    closeDialogs();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function saveEmployee() {
  if (!editedUser.value) return;
  try {
    let response;
    if (editedUser.value.is_employee && editedUser.value.employee) {
      response = await api.put(`/employees/${editedUser.value.employee.id}`, employeeForm.value);
    } else {
      response = await api.post('/employees', employeeForm.value);
    }
    snackbarStore.success(response.data.message);
    fetchUsers();
    closeDialogs();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function toggleUserStatus(user: User) {
  try {
    const payload = { ...user, status: !user.status };
    const response = await api.put(`/users/${user.id}`, payload);
    snackbarStore.success(response.data.message);
    fetchUsers();
  } catch (error) {
    errorStore.handle(error);
  }
}

async function confirmDemote() {
  if (!editedUser.value?.employee) return;
  try {
    const response = await api.delete(`/employees/${editedUser.value.employee.id}`);
    snackbarStore.success(response.data.message || 'Funcionário desvinculado com sucesso.');
    fetchUsers();
    closeDialogs();
  } catch (error) {
    errorStore.handle(error);
  }
}

function closeDialogs() {
  editUserDialog.value = false;
  promoteEmployeeDialog.value = false;
  demoteDialog.value = false;
  nextTick(() => {
    editedUser.value = null;
  });
}

onMounted(fetchUsers);
</script>

<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Gestão de Usuários e Funcionários</h1>
    <p class="text text-body-1 color-subtitle mb-6">Visualize, edite e gerencie as permissões de todos no sistema.</p>

    <v-card flat>
      <v-card-title>
        <v-btn-toggle v-model="filter" mandatory color="primary" variant="outlined">
          <v-btn value="all" class="text">Todos</v-btn>
          <v-btn value="employees" class="text">Apenas Funcionários</v-btn>
          <v-btn value="customers" class="text">Apenas Clientes</v-btn>
        </v-btn-toggle>
      </v-card-title>
      <v-card-text>
        <v-data-table :headers="headers" :items="users" :loading="loading" class="text">
          <template v-slot:item.role="{ item }">
            <v-chip v-if="item.is_employee" color="primary" size="small" variant="tonal">{{ item.employee?.job_title || 'Funcionário' }}</v-chip>
            <span v-else>Cliente</span>
          </template>
          <template v-slot:item.status="{ item }">
            <v-chip :color="item.status ? 'success' : 'grey'" size="small" class="cursor-pointer" @click="toggleUserStatus(item)">
              {{ item.status ? 'Ativo' : 'Inativo' }}
            </v-chip>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn v-if="!item.is_employee" color="primary" variant="tonal" size="small" @click="openPromoteEmployeeDialog(item)">Promover</v-btn>
            <v-btn v-else color="primary" variant="text" size="small" @click="openPromoteEmployeeDialog(item)">Ver/Editar Cargo</v-btn>
            <v-icon icon="mdi-account-edit" size="small" class="ml-2" @click="openEditUserDialog(item)"></v-icon>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>

    <v-dialog v-model="editUserDialog" max-width="500px" persistent>
      <v-card>
        <v-card-title class="subtitle">Editar Usuário</v-card-title>
        <v-card-text>
          <v-text-field v-model="userForm.name" label="Nome"></v-text-field>
          <v-text-field v-model="userForm.email" label="Email"></v-text-field>
          <v-switch v-model="userForm.status" label="Usuário Ativo" color="primary"></v-switch>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialogs">Cancelar</v-btn>
          <v-btn @click="saveUser">Salvar Usuário</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="promoteEmployeeDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span class="subtitle">{{ editedUser?.is_employee ? 'Editar Cargo do Funcionário' : 'Promover a Funcionário' }}</span>
          <v-btn v-if="editedUser?.is_employee" color="error" variant="text" @click="demoteDialog = true"> Remover Cargo </v-btn>
        </v-card-title>
        <v-card-subtitle class="text">{{ editedUser?.name }}</v-card-subtitle>
        <v-card-text>
          <v-text-field v-model="employeeForm.job_title" label="Cargo" class="mb-4"></v-text-field>
          <v-row>
            <v-col cols="12" md="6"><v-text-field v-model="employeeForm.salary" label="Salário" type="number" prefix="R$"></v-text-field></v-col>
            <v-col cols="12" md="6"><v-text-field v-model="employeeForm.hire_date" label="Data de Contratação" type="date"></v-text-field></v-col>
          </v-row>
          <v-textarea v-model="employeeForm.work_schedule" label="Jornada de Trabalho (ex: Seg-Sex, 08h-18h)"></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="text" variant="text" @click="closeDialogs">Cancelar</v-btn>
          <v-btn @click="saveEmployee">Salvar Cargo</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="demoteDialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5 subtitle">Confirmar Ação</v-card-title>
        <v-card-text class="text">Tem certeza que deseja remover o cargo de funcionário de "{{ editedUser?.name }}"? Ele voltará a ser um cliente comum.</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="text" variant="text" @click="demoteDialog = false">Cancelar</v-btn>
          <v-btn color="error" variant="elevated" @click="confirmDemote">Sim, Remover Cargo</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
