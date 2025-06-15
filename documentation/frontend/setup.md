# Guia de Setup do Projeto Frontend

Este guia explica como configurar e rodar o projeto frontend utilizando Vue 3, Vuetify, Pinia, Axios, TypeScript e Sass.

## Pré-requisitos

- [Node.js](https://nodejs.org/) (recomendado: versão 18+)
- [npm](https://www.npmjs.com/) (normalmente vem com o Node.js)

## Instalação

1. **Acesse a pasta do frontend:**
    ```bash
    cd frontend
    ```

2. **Instale as dependências:**
    ```bash
    npm install
    ```

## Rodando o Projeto em Desenvolvimento

```bash
npm run dev
```
O projeto estará disponível em `http://localhost:8080` (ou porta configurada).

## Build para Produção

```bash
npm run build:prod
```
Os arquivos ficarão na pasta `dist/`.

## Lint e Formatação

- **Lint:** Corrige problemas de lint automaticamente.
  ```bash
  npm run lint
  ```
- **Format:** Formata o código com Prettier.
  ```bash
  npm run format
  ```