# 🧁 CupCake Gourmet

Loja virtual de cupcakes artesanais. Projeto Integrador Transdisciplinar em Engenharia de Software II.

- **Front-end:** HTML5, CSS3 e JavaScript (sem frameworks), responsivo.
- **Back-end:** PHP 8.1+ no padrão **MVC**, sem framework.
- **Banco de dados:** MySQL 8 (acesso via PDO com *prepared statements*).
- **Testes:** PHPUnit.

## Funcionalidades (requisitos funcionais)

| Código | Requisito | Onde está implementado |
|---|---|---|
| RF01 / RF02 | Visualizar catálogo e consultar produtos | `CardapioController`, views `cardapio` e `produto` |
| RF03 | Filtrar por categoria | `/cardapio?categoria=ID` (abas) |
| RF04 | Gerenciar carrinho (adicionar, remover, alterar quantidade) | `CarrinhoController`, model `Carrinho` |
| RF05 | Calcular total | `CarrinhoCalculator` e `FreteCalculator` |
| RF06 | Informar entrega | CEP no carrinho (frete simulado por faixa de CEP) |
| RF07 | Selecionar pagamento (cartão ou Pix) | `CheckoutController`, `GatewaySimulado` |
| RF08 | Finalizar compra | `CheckoutService` (transação no banco) |
| RF09 | Informações da loja | `SobreController` |
| Caso de uso | Consultar e enviar avaliação | `AvaliacaoController` |

## Como executar

### 1. Pré-requisitos
PHP 8.1+ (extensões `pdo_mysql` e `mbstring`), MySQL 8 e Composer (somente para os testes).

### 2. Criar o banco
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### 3. Configurar a conexão
Por padrão usa `127.0.0.1:3306`, usuário `root`, senha vazia. Para mudar, defina variáveis de ambiente
(ou edite `config/config.php`):
```bash
export DB_HOST=127.0.0.1 DB_NAME=cupcake_gourmet DB_USER=root DB_PASS=sua_senha
```

### 4. Subir o servidor
```bash
php -S localhost:8000 -t public public/index.php
```
Acesse http://localhost:8000. (Em Apache, aponte o *DocumentRoot* para a pasta `public/`; o `.htaccess` já está incluído.)

### 5. Rodar os testes
```bash
composer install
./vendor/bin/phpunit
```

## Manual de uso (cliente)

1. **Início / Cardápio:** navegue pelos cupcakes; use as abas para filtrar por categoria.
2. **Adicionar ao carrinho:** clique em *Adicionar* (ou abra *Detalhes* e escolha a quantidade).
3. **Carrinho:** altere quantidades, remova itens, informe o **CEP** e clique em *Calcular* para ver a entrega. Se quiser, adicione um comentário.
4. **Próximo:** vai para o pagamento. Informe nome e e-mail e escolha **Cartão** ou **Pix**.
5. **Pagar:** o pagamento é **simulado** (nada é cobrado). Ao aprovar, aparece a confirmação do pedido.
6. **Avaliações:** em *Início*, *Sobre Nós* ou na página do produto, clique em *Enviar avaliação*.

### Dados para teste
| Situação | Como provocar |
|---|---|
| Pagamento aprovado (cartão) | `4111 1111 1111 1111`, validade futura (ex.: `12/30`), CVV `123` |
| Pagamento recusado (cartão) | `4000 0000 0000 0002` |
| Pagamento via Pix | Sempre aprovado |
| Frete | CEP iniciado em `0` = R$ 8,00 · `1` = R$ 12,00 · demais = R$ 25,00 |

### Mensagens de erro tratadas
Carrinho vazio, quantidade inválida (mínimo 1, máximo 99), CEP inválido, campo obrigatório vazio
(indicando qual), e-mail inválido, número de cartão inválido (Luhn), validade vencida, CVV inválido,
pagamento recusado, produto inexistente, página não encontrada (404), sessão expirada (CSRF) e falha de banco.
As validações rodam no navegador (`public/js/app.js`) e **de novo no servidor** (`Validator`).

## Arquitetura (MVC)

```
public/            Ponto de entrada (index.php), CSS, JS e imagens
app/
  Core/            Router, View, Controller base, Database, Validator, Csrf, Flash, Config
  Models/          Acesso ao banco: Produto, Categoria, Carrinho, Pedido, Pagamento, Cliente, Avaliacao
  Views/           Telas (layout + páginas + partials)
  Controllers/     Recebem a requisição, chamam Models/Services e escolhem a View
  Services/        Regras de negócio: FreteCalculator, CarrinhoCalculator, GatewaySimulado, CheckoutService
config/            Configuração (banco via variáveis de ambiente)
database/          schema.sql (projeto físico) e seed.sql (dados de exemplo)
tests/Unit/        Testes PHPUnit
docs/              Documentação por situação-problema
```

Fluxo: `Requisição → Router → Controller → (Model / Service) → View → HTML`.
O fluxo de finalizar compra segue o diagrama de sequência em `docs/situacao-1/`.

## Segurança
- SQL com *prepared statements* (PDO) — sem concatenar dados do usuário.
- Saída HTML sempre escapada (`e()`), contra XSS.
- Token CSRF em todos os formulários POST.
- Dados de cartão **não são armazenados**: só método, status e referência da transação.
- Redirecionamentos só para caminhos internos; confirmação de pedido visível só ao comprador.

## Limitações conhecidas (escopo desta versão)
- Pagamento e frete são **simulados** (sem integração real).
- Sem login de cliente: o cliente é identificado pelo e-mail informado no checkout.
- Pagamento por cartão de crédito e Pix (cartão de débito não implementado).
- Imagens de produto: coloque arquivos em `public/img/` e preencha `produto.imagem_url` (ex.: `img/chocolate.jpg`); sem imagem, a tela mostra um ícone.

## Organização no Git
```
docs/situacao-1/   Kaizen, requisitos, UML, IHC, modelo de dados, dicionário
docs/situacao-2/   Notas de desenvolvimento e decisões técnicas
docs/situacao-3/   Testes, formulário dos 5 testadores, correções, vídeo
```
Sugestão de commits pequenos e descritivos (`feat:`, `fix:`, `docs:`, `test:`), um por funcionalidade.
# cupcake-gourmet
