# Deploy no EasyPanel (Hostinger VPS)

Este projeto sobe como um único contêiner Docker (PHP-FPM + Nginx), construído a partir do `Dockerfile` na raiz do repositório. O EasyPanel builda a imagem direto do GitHub a cada deploy.

## 1. Banco de dados

Você já criou o serviço de banco (MySQL) no projeto `projetos-de-clientes` do EasyPanel, com:

- Banco: `agroraiz_bd_erp`
- Usuário: `agroraiz_user`
- Host interno: `projetos-de-clientes_agroraiz_bancodados`
- Porta: `3306`

Esse host interno só resolve porque o app e o banco estão no **mesmo projeto** do EasyPanel (mesma rede Docker). Não mude o serviço de projeto sem atualizar o `DB_HOST`.

## 2. Serviço da aplicação (App)

No EasyPanel, dentro do mesmo projeto:

1. **Create Service → App**.
2. **Source**: GitHub → repositório `KaioADalfior/agroraizERP`, branch `main`.
3. **Build**: método **Dockerfile** (o EasyPanel já detecta o `Dockerfile` na raiz).
4. **Environment Variables**: cole o conteúdo do `.env` de produção (veja abaixo). O EasyPanel injeta essas variáveis no contêiner.
5. **Domains**: adicione `agroraiz.daksolucoes.com.br` apontando para a **porta 8080** do contêiner (é a porta em que a imagem já escuta). Ative **HTTPS** (o EasyPanel emite o certificado Let's Encrypt automaticamente, desde que o DNS do domínio já aponte para o IP da VPS).
6. Clique em **Deploy**.

### `.env` de produção

```dotenv
APP_NAME="Agro Raiz"
APP_ENV=production
APP_KEY=base64:8LSjn6Z7nYQ+qlhjsu156b8Jt5hb90voJyfuqyXOK8k=
APP_DEBUG=false
APP_URL=https://agroraiz.daksolucoes.com.br
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=pt_BR

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=projetos-de-clientes_agroraiz_bancodados
DB_PORT=3306
DB_DATABASE=agroraiz_bd_erp
DB_USERNAME=agroraiz_user
DB_PASSWORD=coloque-aqui-a-senha-real-do-banco

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@agroraiz.daksolucoes.com.br"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

Troque `DB_PASSWORD` pela senha real do serviço de banco (veja em **Senha**, não **Senha Root**, na tela do serviço de banco no EasyPanel) assim que definir uma senha forte.

> Nunca use `APP_DEBUG=true` em produção: qualquer erro passaria a mostrar código-fonte, caminhos do servidor e variáveis de ambiente para quem visitar o site.

## 3. O que acontece automaticamente no deploy

O `Dockerfile` builda os assets do Tailwind (`npm run build`), instala as dependências do PHP com Composer, e a imagem final (baseada em `serversideup/php:8.3-fpm-nginx`) roda, a cada início de contêiner:

- `php artisan migrate --force` (cria/atualiza as tabelas do banco)
- `php artisan storage:link`
- `php artisan config:cache`, `route:cache`, `view:cache`

Ou seja: **não precisa rodar comandos manuais** a cada deploy — só dar `git push` para a branch `main` e clicar em **Deploy** no EasyPanel (ou configurar deploy automático via webhook, na aba **Source** do serviço).

## 4. Criar o primeiro usuário de acesso

O sistema não tem cadastro público. Depois do primeiro deploy bem-sucedido, abra o **Terminal/Console** do serviço da aplicação no EasyPanel e rode:

```sh
php artisan agroraiz:usuario --nome="Seu Nome" --email=voce@agroraiz.com.br --senha=uma-senha-forte
```

Depois acesse `https://agroraiz.daksolucoes.com.br/login`.

## 5. Solução de problemas

- **`open Dockerfile: no such file or directory`** — o `Dockerfile` ainda não estava no repositório GitHub no momento do build. Confirme que o arquivo está na raiz do repositório na branch `main` e rode o deploy de novo.
- **Erro de acesso ao banco (`Access denied`)** — confira `DB_HOST`, `DB_USERNAME` e `DB_PASSWORD` nas variáveis de ambiente do app; eles têm que ser idênticos aos do serviço de banco.
- **Tela em branco ou erro 500** — veja os logs do serviço no EasyPanel (aba **Logs**). Como `APP_DEBUG=false`, o site não mostra o erro para o visitante, só no log.
- **CSS/Tailwind não carrega** — geralmente é o `npm run build` falhando no build da imagem; veja o log de build do EasyPanel (aba **Deployments**).
