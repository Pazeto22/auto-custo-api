# AutoCusto - Sistema de Orçamentos para Oficinas Mecânicas

## Descrição

O AutoCusto é um sistema web desenvolvido em Laravel para auxiliar oficinas mecânicas na criação, organização e gestão de orçamentos de serviços e peças. O sistema foi pensado para ser simples, seguro e escalável, permitindo que o dono da oficina controle clientes, veículos, produtos, orçamentos e seus itens de forma eficiente.

## Premissas do Projeto

-   Cada oficina (workshop) possui seus próprios usuários e clientes.
-   Usuários só visualizam e gerenciam dados da sua própria oficina.
-   Veículos pertencem a clientes, que por sua vez pertencem a uma oficina.
-   Orçamentos pertencem a um cliente, veículo e usuário (quem criou/enviou).
-   Itens de orçamento (BudgetItem) só existem dentro de um orçamento.
-   Produtos podem ser serviços ou peças.
-   Todas as operações são feitas via API RESTful.

## Rotas e Endpoints

### Oficinas (Workshops)

-   `GET    /api/workshops` — Lista oficinas
-   `POST   /api/workshops` — Cria oficina
-   `GET    /api/workshops/{workshop}` — Detalha oficina
-   `PUT    /api/workshops/{workshop}` — Atualiza oficina
-   `DELETE /api/workshops/{workshop}` — Remove oficina

### Usuários (Users) — Aninhado em Workshop

-   `GET    /api/workshops/{workshop}/users` — Lista usuários da oficina
-   `POST   /api/workshops/{workshop}/users` — Cria usuário na oficina
-   `GET    /api/workshops/{workshop}/users/{user}` — Detalha usuário
-   `PUT    /api/workshops/{workshop}/users/{user}` — Atualiza usuário
-   `DELETE /api/workshops/{workshop}/users/{user}` — Remove usuário

##### Filtros e buscas:

-   `?q=termo` — Busca por nome, email ou telefone
-   `?per_page=10` — Paginação

### Clientes (Clients) — Aninhado em Workshop

-   `GET    /api/workshops/{workshop}/clients` — Lista clientes da oficina
-   `POST   /api/workshops/{workshop}/clients` — Cria cliente
-   `GET    /api/workshops/{workshop}/clients/{client}` — Detalha cliente
-   `PUT    /api/workshops/{workshop}/clients/{client}` — Atualiza cliente
-   `DELETE /api/workshops/{workshop}/clients/{client}` — Remove cliente

##### Filtros e buscas:

-   `?q=termo` — Busca por nome, telefone ou email
-   `?per_page=10` — Paginação

### Veículos (Vehicles) — Aninhado em Client

-   `GET    /api/clients/{client}/vehicles` — Lista veículos do cliente
-   `POST   /api/clients/{client}/vehicles` — Cria veículo
-   `GET    /api/clients/{client}/vehicles/{vehicle}` — Detalha veículo
-   `PUT    /api/clients/{client}/vehicles/{vehicle}` — Atualiza veículo
-   `DELETE /api/clients/{client}/vehicles/{vehicle}` — Remove veículo

##### Filtros e buscas:

-   `?q=termo` — Busca por marca, modelo ou placa
-   `?per_page=10` — Paginação

### Produtos (Products)

-   `GET    /api/products` — Lista produtos
-   `POST   /api/products` — Cria produto
-   `GET    /api/products/{product}` — Detalha produto
-   `PUT    /api/products/{product}` — Atualiza produto
-   `DELETE /api/products/{product}` — Remove produto

##### Filtros e buscas:

-   `?q=termo` — Busca por nome ou descrição
-   `?type=service|part` — Filtra por tipo
-   `?min_price=valor` — Preço mínimo
-   `?max_price=valor` — Preço máximo
-   `?per_page=10` — Paginação

### Orçamentos (Budgets)

-   `GET    /api/budgets` — Lista orçamentos
-   `POST   /api/budgets` — Cria orçamento
-   `GET    /api/budgets/{budget}` — Detalha orçamento (inclui cliente, veículo, usuário e itens)
-   `PUT    /api/budgets/{budget}` — Atualiza orçamento
-   `DELETE /api/budgets/{budget}` — Remove orçamento

##### Filtros e buscas:

-   `?client_id=ID` — Filtra por cliente
-   `?vehicle_id=ID` — Filtra por veículo
-   `?user_id=ID` — Filtra por usuário
-   `?status=sketch|pending|approved|rejected` — Filtra por status
-   `?q=termo` — Busca por texto em more_information
-   `?per_page=10` — Paginação

### Itens do Orçamento (Budget Items) — Aninhado em Budget

-   `GET    /api/budgets/{budget}/items` — Lista itens do orçamento
-   `POST   /api/budgets/{budget}/items` — Cria item
-   `GET    /api/budgets/{budget}/items/{item}` — Detalha item
-   `PUT    /api/budgets/{budget}/items/{item}` — Atualiza item
-   `DELETE /api/budgets/{budget}/items/{item}` — Remove item

## Observações

-   Todas as rotas retornam JSON.
-   Todas as operações de criação e atualização validam os dados enviados.
-   Os relacionamentos são respeitados em todas as operações (ex: só é possível acessar clientes de uma oficina, veículos de um cliente, etc).
-   Recomenda-se proteger as rotas com autenticação e autorização para uso em produção.
