<script setup lang="ts">
import { PhPackage, PhLeaf, PhUsers, PhCreditCard, PhReceipt, PhCalendarCheck } from '@phosphor-icons/vue';

interface DashboardCard {
  icon: any;
  title: string;
  description: string;
  to: string;
  color: string;
}

const cards: DashboardCard[] = [
  {
    icon: PhPackage,
    title: 'Produtos e Categorias',
    description: 'Gerencie o cardápio principal e como os itens são agrupados.',
    to: '/admin/products',
    color: 'primary'
  },
  {
    icon: PhLeaf,
    title: 'Ingredientes e Fornecedores',
    description: 'Controle o estoque, fornecedores e unidades de medida.',
    to: '/admin/ingredients',
    color: 'success'
  },
  {
    icon: PhUsers,
    title: 'Usuários e Funcionários',
    description: 'Visualize clientes e defina quem tem acesso administrativo.',
    to: '/admin/users',
    color: 'indigo-darken-2'
  },
  {
    icon: PhCreditCard,
    title: 'Pagamentos e Cupons',
    description: 'Defina os métodos de pagamento e crie cupons de desconto.',
    to: '/admin/payment',
    color: 'deep-purple-darken-1'
  },
  {
    icon: PhReceipt,
    title: 'Pedidos',
    description: 'Gerencie todos os pedidos em tempo real e altere seus status.',
    to: '/admin/orders',
    color: 'blue-darken-2'
  },
  {
    icon: PhCalendarCheck,
    title: 'Reservas e Mesas',
    description: 'Administre as reservas dos clientes e a configuração das mesas.',
    to: '/admin/reservations',
    color: 'brown-darken-2'
  }
];
</script>

<template>
  <v-container fluid>
    <h1 class="title text-h4 mb-2">Painel Administrativo</h1>
    <p class="text text-body-1 color-subtitle mb-8">Selecione uma área abaixo para começar a gerenciar.</p>

    <v-row>
      <v-col v-for="card in cards" :key="card.title" cols="12" sm="6" lg="4">
        <v-card :to="card.to" hover class="d-flex flex-column fill-height pa-2" variant="outlined">
          <v-card-item class="flex-grow-0">
            <template #prepend>
              <v-avatar :color="card.color" size="48" class="mr-2">
                <component :is="card.icon" :size="26" color="white" weight="bold" />
              </v-avatar>
            </template>
            <v-card-title class="subtitle pa-0">{{ card.title }}</v-card-title>
          </v-card-item>
          <v-card-text class="text flex-grow-1 py-3">{{ card.description }}</v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn :color="card.color" variant="text" append-icon="mdi-arrow-right">Acessar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
