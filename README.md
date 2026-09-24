# Intranet Hospital Santa Aurora

Intranet corporativa em **Joomla 6** (PHP 8.3, MariaDB 11.4) rodando em Docker.
Plano do projeto e decisões: [PLANO.md](PLANO.md).

## Requisitos

- Docker com Compose v2
- `curl` e acesso a `update.joomla.org` na primeira instalação (download do idioma pt-BR).
  Sem internet, coloque o pacote pt-BR em `var/packages/vendor/pt-BR.zip`.

## Instalação

```bash
scripts/install.sh           # estrutura vazia, pronta para receber conteúdo
scripts/install.sh --demo    # + conteúdo de demonstração (sistemas, notícias, protocolos, aviso, eventos)
```

O script:

1. Cria o `.env` a partir do `.env.example`, com senhas aleatórias.
2. Sobe os containers. A imagem oficial instala o Joomla sozinha no primeiro start.
3. Aplica a configuração global: URLs amigáveis sem `index.php`, fuso `America/Sao_Paulo`, sessão de 30 min, debug desligado.
4. Instala o idioma pt-BR e o define como padrão no site e no administrador.
5. Instala os pacotes da intranet encontrados em `var/packages/*.zip`.
6. Em desenvolvimento, registra as extensões de `src/`, montadas pelo `docker-compose.override.yml`.
7. Roda `intranet:setup`, que cria o template padrão, as categorias, os menus e os módulos. Só cria o que falta.

Pode ser executado de novo sem problema: cada passo verifica se já foi feito.

| | Endereço |
|---|---|
| Site | http://localhost:8080/ |
| Administrador | http://localhost:8080/administrator — usuário `admin`, senha em `ADMIN_PASSWORD` no `.env` |
| E-mails capturados (dev) | http://localhost:8025/ |

## Comandos úteis

```bash
scripts/reset.sh [--demo]        # apaga banco e arquivos e reinstala do zero (pede confirmação)
scripts/visual-check.sh          # screenshots desktop/tablet/mobile + axe (acessibilidade) em var/visual/
scripts/build-icons.sh           # regenera o sprite de ícones a partir de scripts/icons.txt
scripts/build-demo-assets.py     # regenera as imagens e os PDFs do conteúdo de demonstração
docker compose exec -u www-data joomla php cli/joomla.php intranet:setup        # recria o que faltar
docker compose exec -u www-data joomla php cli/joomla.php intranet:acl-report   # permissões dos papéis
tests/acl/login-test.sh          # testa o painel com login real de cada papel
tests/ramais/admin-test.sh       # testa o cadastro de ramais pelo painel (validação, ativar/desativar, excluir)
docker compose logs -f joomla    # logs do Apache/PHP
docker compose exec -u www-data joomla php cli/joomla.php list   # CLI do Joomla
```

## Estrutura

```
docker-compose.yml            joomla + db (MariaDB) + mailpit
docker-compose.override.yml   dev: monta src/ dentro do Joomla (edição ao vivo)
.env.example                  variáveis (o .env real não vai para o controle de versão)
src/
  templates/hospital_intranet/               template (PHP, overrides, layouts, idioma)
  media/templates/site/hospital_intranet/    CSS, JS, fontes, ícones, imagens
  components/com_ramais/                     diretório de ramais (administrator/, site/, media/)
  plugins/console/intranet/                  comandos intranet:setup e intranet:acl-report
  plugins/system/intranet/                   regras extras de ACL no painel
scripts/                      install, reset, visual-check, build-icons
tests/                        visual/ (screenshots + axe), acl/ (login real por papel), ramais/ (cadastro)
docs/                         acl.md (papéis, permissões e campos)
var/                          pacotes baixados, screenshots (ignorado)
```

## Design system

Catálogo de componentes e estados: http://localhost:8080/?tmpl=designsystem.
Tokens (cores, fontes, espaçamentos) ficam em `src/media/templates/site/hospital_intranet/css/variables.css`.

## Página inicial

As seções da home são módulos **Artigos** (Conteúdo → Módulos do site) com layouts do template:

| Seção | Posição | Conteúdo |
|---|---|---|
| Avisos importantes (no topo) | `alerts` | Categoria Avisos, dentro do período de publicação; ordenados por prioridade |
| Acesso rápido | `quick-access` | Sistemas marcados como **Destaque**; a ordem é a de *Artigos em destaque* |
| Últimas notícias | `news` | 3 mais recentes da categoria Notícias |
| Últimos protocolos | `protocols` | 4 mais recentes de Protocolos e POPs |

No título do módulo, o texto antes de `|` vira o rótulo pequeno da seção: `Fique por dentro | Últimas notícias`.

## Páginas internas

As páginas de categoria e de artigo usam overrides do template, escolhidos pela **seção** da categoria. A seção é identificada pela nota `intranet:cat:*` da categoria raiz, então renomear uma categoria não muda o layout.

| Página | Menu | O que mostra |
|---|---|---|
| Sistemas | `/sistemas` | Cards agrupados por subcategoria, com busca e botão "Acessar sistema" |
| Notícias | `/noticias` | Abas por categoria, cards e paginação (9 por página) |
| Documentos | `/documentos` | Biblioteca inteira com busca (código, título, assunto) e filtros por tipo, setor e status |
| Protocolos | `/protocolos` | A mesma biblioteca, só com Protocolos e POPs |
| Documento | — | Ficha com "Baixar documento", status, versão, setor, datas e público-alvo |
| Notícia | — | Categoria, data, imagem e texto |

Os itens de menu de categoria têm, na aba **Intranet**, a opção **Mostrar só estas subcategorias**, adicionada pelo plugin *Sistema - Intranet*. É com ela que o item Protocolos mostra só Protocolos + POPs.

Os filtros e buscas funcionam em tempo real, com JavaScript, ou pelo servidor, sem JavaScript, pelos parâmetros `?q=&tipo=&setor=&status=`.

**Arquivos da Biblioteca:** envie pelo Gerenciador de Mídia para `images/documentos` e escolha o arquivo no campo **Arquivo** do documento.

## Diretório de ramais

Cadastro em **Componentes → Ramais** (só Administrador). Campos: Setor, Ramal, Localização, Status (Ativo/Inativo) e Ordem.

- **Ramal:** só números. Para mais de um, separe com barra: `2015 / 2016`.
- **Status:** ramais inativos não aparecem no site.
- **Ordem:** usada quando o item de menu *Ramais* está com a opção "Ordem personalizada". O padrão é Setor, de A a Z.

No site (`/ramais`):
- **Busca:** filtra enquanto se digita, sem diferenciar maiúsculas e acentos.
- **Ordenação:** clicar no cabeçalho de cada coluna.
- **Links:** o endereço guarda a busca e a ordem, e pode ser compartilhado (`/ramais?q=uti`).
- **Sem JavaScript:** a página funciona do mesmo jeito, pelo servidor.

Registros criados pelo `intranet:setup` têm a nota `intranet:<chave>` no painel. **Não altere essas notas**: é por elas que o setup reconhece o que já existe.
