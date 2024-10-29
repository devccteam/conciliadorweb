
## Configuração do Projeto

Esta documentação guiará você na configuração eficiente do ambiente local para desenvolvimento do projeto Conferire. Siga os passos abaixo para configurar o projeto no seu ambiente:

1. **Clone o Repositório:**

   ```bash
   git clone https://github.com/devccteam/conferire.git

   cd conferire

   code .
   ```

2. **Crie os Arquivos .env:**
   ```bash
   cp .env.example .env
   ```

3. **Atualize as Variáveis de Ambiente no Arquivo .env de acordo com suas configurações locais:**
   ```dotenv
   DB_CONNECTION=pgsql
   DB_HOST=pgsql
   DB_PORT=5432
   DB_DATABASE=conferire
   DB_USERNAME=postgres
   DB_PASSWORD=root
   ```

4. **Instale as Dependências do Projeto:**
   ```bash
   composer install
   ```

5. **Gerar a Chave do Projeto Laravel:**
   ```bash
   php artisan key:generate
   ```

6. **Acesse o Projeto:**
   - Abra o navegador e visite http://localhost:8000, deve aparecer a tela de login do painel da aplicação.

7. **Excecute as Migrations com as seeders:**
    ```bash
    php artisan migrate --seed
    ```