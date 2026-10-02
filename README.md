# epd048-lab-2026-2

Projeto Laravel usado como **base para as aulas do LLM Lab**. Ele não é um produto: é o ponto de partida dos [roteiros](#roteiros-e-tags) em que os alunos pedem código a um agente (OpenCode) e comparam o resultado entre modelos de linguagem.

O projeto reúne:

- **Laravel 13** com o **starter kit Livewire** (login, cadastro, painel e configurações de conta já prontos).
- **Laravel Boost**, que liga o agente de código ao projeto (regras do Laravel e ferramentas para consultar rotas, banco e logs).
- **Pest** para os testes e **SQLite** como banco.
- Quatro **comandos Artisan** para gerenciar usuários pelo terminal, descritos [abaixo](#comandos-de-usuário).

## Roteiros e tags

Cada roteiro parte de uma **tag** deste repositório, para que todos comecem do mesmo código, mesmo que a `main` avance depois.

| Tag | Roteiro | O que contém |
| --- | --- | --- |
| `roteiro-05` | Chat no terminal com o OpenCode | O projeto base, com os comandos de usuário abaixo. |

Para começar um roteiro, clone a tag:

```bash
git clone --branch roteiro-05 https://github.com/prof-anderson-trindade/epd048-lab-2026-2.git roteiro05
cd roteiro05
```

O Git avisa que você está em *detached HEAD*; isso é esperado. Crie um branch para trabalhar, sempre a partir da tag: `git switch -c meu-branch roteiro-05`.

## Instalação

O banco é um arquivo SQLite que não vem no repositório. Crie-o **antes** de qualquer outra coisa:

```bash
touch database/database.sqlite
composer setup
```

O `composer setup` instala as dependências PHP, cria o `.env` (se ainda não existir), gera a chave da aplicação, roda as migrações, instala as dependências JavaScript e gera o CSS e o JavaScript do site.

Para subir o projeto em desenvolvimento:

```bash
composer run dev
```

Os testes:

```bash
php artisan test --compact
```

## Comandos de usuário

Os comandos criam, listam, alteram a senha e excluem usuários direto do terminal, com [Laravel Prompts](https://laravel.com/framework/docs/prompts).

| Comando | O que faz |
| --- | --- |
| `php artisan user:create` | Cria um usuário. Pergunta nome, e-mail e senha (com confirmação). |
| `php artisan user:list` | Lista os usuários e permite agir sobre um deles. |
| `php artisan user:password` | Altera a senha de um usuário. |
| `php artisan user:delete` | Exclui um usuário. |

### `user:create`

```bash
php artisan user:create
```

Pergunta, nesta ordem, o **nome**, o **e-mail** (válido e ainda não cadastrado), a **senha** e a **confirmação da senha**. A senha segue a regra padrão do projeto (`Password::default()`), que em produção exige no mínimo 12 caracteres, com letras maiúsculas e minúsculas, números e símbolos.

### `user:list`

```bash
php artisan user:list [busca] [--limit=50]
```

- **No terminal**, abre uma tabela navegável: setas e `PageUp`/`PageDown` rolam, `/` filtra, `Enter` escolhe um usuário. Em seguida, um menu oferece **Alterar a senha**, **Excluir o usuário**, **Voltar à lista** ou **Sair**. As duas primeiras ações chamam `user:password` e `user:delete` já com o usuário escolhido.
- **Sem terminal** (`--no-interaction` ou saída redirecionada), imprime uma tabela estática com no máximo `--limit` linhas e avisa se houver mais.
- `busca` filtra por nome ou e-mail.

### `user:password`

```bash
php artisan user:password [--user=ID_OU_EMAIL]
```

Sem `--user`, abre uma busca por nome ou e-mail (mínimo de 2 caracteres). Depois pede a nova senha e a confirmação. A nova senha é digitada no terminal, então o comando não roda com `--no-interaction`.

### `user:delete`

```bash
php artisan user:delete [--user=ID_OU_EMAIL] [--force]
```

Sem `--user`, abre a mesma busca. Pede confirmação (a resposta padrão é "não"). Com `--force`, exclui sem perguntar, o que também permite usar o comando sem interação:

```bash
php artisan user:delete --user=maria@example.com --force --no-interaction
```

> [!WARNING]
> A exclusão não pode ser desfeita, e o projeto não impede excluir o último usuário.

Os comandos estão em `app/Console/Commands/` e os testes em `tests/Feature/CreateUserCommandTest.php`.

## Configuração local

Estes arquivos são gerados em cada máquina e o Git os ignora: `.env`, `database/database.sqlite`, `opencode.json`, `AGENTS.md`, `boost.json` e `.mcp.json`. Para o agente de código ler o projeto, rode `php artisan boost:install` e deixe o **OpenCode** marcado.

Nunca coloque chaves de API no código nem em arquivos versionados.
