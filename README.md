# Agro Raiz — Sistema de gestão de clientes e análises de solo

Sistema web (ERP) da **Agro Raiz — Consultorias e Projetos**, feito com **Laravel**, **Tailwind CSS 4** e **Alpine.js**, usando **MySQL/MariaDB**.

O sistema substitui a planilha *Interpretação de Análise de Solo em Gráficos*. Módulos previstos (espelham as abas da planilha):

| Módulo | Situação |
| --- | --- |
| Base, identidade visual, login e painel | Pronto |
| Clientes (ID, cliente/proprietário, cidade/estado, data, observação) | Próxima etapa |
| Análises de solo (talhões, profundidade, cálculos de SB, CTC, V%, m%) | Planejado |
| Gráficos de interpretação por amostra | Planejado |
| Banco de dados de teores adequados por cultura | Planejado |

## Requisitos

- PHP 8.3 ou superior e Composer
- Node.js 20.19 ou superior (ou 22+), com npm
- MySQL ou MariaDB rodando (Laragon, XAMPP, Herd + DBngin, Docker etc.)

## Primeira instalação

```powershell
composer install
npm install
copy .env.example .env      # só se ainda não existir o .env
php artisan key:generate    # só se o .env for novo
```

Confira no `.env` os dados do banco (por padrão: banco `agroraiz`, usuário `root`, sem senha):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agroraiz
DB_USERNAME=root
DB_PASSWORD=
```

Depois:

```powershell
php artisan migrate                # cria as tabelas (e o banco, se não existir)
php artisan agroraiz:usuario       # cria o seu usuário de acesso
```

## Rodando em desenvolvimento

Em dois terminais:

```powershell
php artisan serve
npm run dev
```

Acesse <http://localhost:8000>. Para gerar os arquivos finais (produção): `npm run build`.

## Usuários

Não existe cadastro público: o acesso é criado pelo administrador com o comando abaixo. Rodar de novo com o mesmo e-mail redefine nome e senha.

```powershell
php artisan agroraiz:usuario
php artisan agroraiz:usuario --nome="Maria Souza" --email=maria@agroraiz.com.br --senha=uma-senha-forte
```

## Identidade visual

A cor da marca é o verde oliva da logo, `#4B5942`, cadastrado no Tailwind como **`raiz-700`** (veja `resources/css/app.css`). A escala vai de `raiz-50` (quase branco) a `raiz-950` (quase preto): `bg-raiz-700`, `text-raiz-800`, `border-raiz-200` etc. Os títulos usam a fonte `font-display` (Michroma), parecida com a da logo.

Componentes prontos em `resources/views/components`: `x-layouts.app`, `x-layouts.guest`, `x-page-header`, `x-card`, `x-button`, `x-input`, `x-nav-link`, `x-icon`, `x-logo`.

Os arquivos da logo ficam em `public/images` (a logo branca com fundo transparente deve ser usada sempre sobre fundo verde).

## Testes

```powershell
php artisan test
```

## Idioma e fuso

O sistema roda em português do Brasil (`APP_LOCALE=pt_BR`) e no fuso `America/Sao_Paulo`. As traduções ficam em `lang/pt_BR`.
