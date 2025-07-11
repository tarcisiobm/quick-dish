## Como rodar o projeto Backend (Laravel + PHP)

### Requisitos

- [PHP](https://www.php.net/) (versão **8.2** ou superior)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/)
- Variáveis de ambiente configuradas (ver `.env.example`)

### Passos para execução

1. **Acesse o diretório do backend:**
    ```bash
    cd backend
    ```

2. **Instale as dependências:**
    ```bash
    composer install
    ```

3. **Configure as variáveis de ambiente:**
    - O Composer pode copiar automaticamente o `.env.example` para `.env` na instalação inicial. Se não existir, faça manualmente:
      ```bash
      cp .env.example .env
      ```
    - Ajuste as variáveis de ambiente, especialmente:
      ```
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=quick_dish
      DB_USERNAME=root
      DB_PASSWORD=

      ```

4. **Gere a chave da aplicação:**
    ```bash
    php artisan key:generate
    ```

5. **Execute as migrações do banco de dados:**
    ```bash
    php artisan migrate
    ```

6. **Inicie o servidor de desenvolvimento:**
    ```bash
    php artisan serve
    ```

O backend estará rodando em `http://localhost:3100` (ou porta configurada).
