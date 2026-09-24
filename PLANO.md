# Plano — Intranet Hospital Santa Aurora (Joomla)

Baseado em `prompt_intranet_hospitalar_joomla.md`. Este documento define **o que será construído, como e em que ordem**, e registra as decisões tomadas onde a especificação é ambígua ou contraditória.

---

## 1. Decisões de base

| Tema | Decisão | Motivo |
|---|---|---|
| Versão | **Joomla 6** (PHP 8.3), código escrito só com a API moderna (namespaces, sem camada legada) → também roda em Joomla 5.4 | Versão estável atual; evita retrabalho de migração |
| Ambiente | Docker Compose: `joomla:6-php8.3-apache` + `mariadb:11` + `mailpit` (dev) | PHP local é 8.1 — insuficiente; container isola tudo |
| Acesso ao frontend | **Sem login.** Colaborador acessa direto | §32 e §37 (decisão mais recente) |
| Acesso ao backend | `/administrator` com login + MFA, só para 3 papéis | §26, §32, §37 |
| Proteção da rede | Frontend e `/administrator` acessíveis **somente pela rede interna** (restrição por IP no Apache/proxy) | Sem login, os documentos ficariam públicos se o site fosse exposto à internet |
| Conteúdo | Tudo administrável: Articles + Categories + Custom Fields sempre que possível; código próprio só onde o nativo não atende | §1, §25, §33 |

### Contradições na especificação e como foram resolvidas

1. **Login do colaborador** — §4 (avatar "João da Silva ▾"), §5 ("Bom dia, João!") e §17 (dashboard pós-login) conflitam com §32/§37 (sem login no frontend).
   → Removidos: menu do usuário, saudação com nome e dashboard pessoal. A saudação continua dinâmica pelo horário ("Bom dia!").
2. **Grupos de usuários** — §12 (TI, RH, Enfermagem, Corpo Clínico, Gestão, Colaborador) conflita com §26/§37 (só Administrador, Qualidade e Educação Permanente, Imprensa; "não criar usuários para colaboradores").
   → Só os 3 papéis administrativos. "Público-alvo" dos documentos e "público/setor" dos avisos viram **campos informativos/filtros**, não restrição de acesso.
3. **Ramais** — §26 fala em "cadastrar colaboradores e setores"; §13 define exatamente Setor / Ramal / Localização.
   → Vale §13: sem cadastro de pessoas.
4. **Menu** — "Protocolos" e "Documentos" são itens separados, mas protocolos são um tipo de documento.
   → Uma única biblioteca; "Protocolos" é uma visão filtrada dela (tipos Protocolo + POP).
5. **"Personalizar atalhos"** aparece na imagem de referência, mas depende de login do colaborador.
   → Omitido. O link do painel de acesso rápido vira "Ver todos os sistemas ›".

---

## 2. Modelo de conteúdo

Nada fica fixo no template. Cada tipo de conteúdo tem uma fonte administrável:

| Conteúdo | Implementação | Campos próprios (Custom Fields) |
|---|---|---|
| **Notícias** | Articles, categoria `Notícias` com subcategorias (Institucional, Pessoas, Saúde e Bem-estar, Tecnologia, RH, Eventos, Comunicados, TI) + Tags | — (imagem de introdução, data e resumo são nativos) |
| **Documentos / Protocolos** | Articles, categoria `Biblioteca` com subcategorias por tipo (Protocolos, POPs, Manuais, Formulários, Políticas, Normas, Fluxogramas, Treinamentos, RH, TI) | código, setor responsável, versão, data de revisão, arquivo (campo Media, tipo documento), status (vigente/em revisão/obsoleto), público-alvo |
| **Sistemas / Acesso rápido** | Articles, categoria `Sistemas` com subcategorias (Assistenciais, Administrativos, RH, Financeiro, TI, BI e Indicadores). Descrição = texto de introdução; ordem, publicado e nível de acesso são nativos | URL, ícone, cor do ícone (paleta fixa), abrir em nova aba. Acesso rápido da home = artigos em **Destaque**, ordem em "Artigos em destaque" |
| **Eventos** | Articles, categoria `Eventos` | data, hora, local, responsável, link |
| **Avisos** | Articles, categoria `Avisos`. Período de exibição = *Início/Fim da publicação* (nativo) | prioridade (info/atenção/crítico), público, setor |
| **Ramais** | **Componente próprio `com_ramais`** (tabela `#__ramais`) | setor, ramal, localização, status, ordem |
| Menus, imagens, usuários, permissões | Menu Manager, Media Manager, Users, ACL — nativos | — |

**Por que os ramais são o único componente próprio:** como artigos, seriam centenas de registros com uma entrada cada, sem busca nem ordenação por coluna. Um componente simples dá a lista administrativa padrão do Joomla (filtro, ordenação, ativar/desativar em lote) e uma página rápida no frontend.

---

## 3. Papéis e ACL (menor privilégio)

| Grupo Joomla | Pai | Pode no backend |
|---|---|---|
| **Administrador** | Super Users | Tudo |
| **Qualidade e Educação Permanente** | Registered + `core.login.admin` | Criar, editar e publicar na categoria `Biblioteca`; enviar arquivos para `images/documentos` e `files/` |
| **Imprensa** | Registered + `core.login.admin` | Criar, editar e publicar em `Notícias`, `Eventos` e `Avisos`; Media Manager (imagens) |

- As permissões são dadas **por categoria** (ACL do `com_content`). Os grupos não têm permissão global de edição.
- Ramais, Sistemas, Menus, Módulos, Usuários e Configuração ficam só com o Administrador (§13.7). A permissão pode ser concedida depois, pelo ACL, sem mexer em código.
- O menu do backend de cada papel mostra só o que o papel usa: o próprio Joomla esconde as telas sem permissão.
- O ACL nativo não separa "editar artigos da categoria" de "editar a categoria". O plugin `plg_system_intranet` bloqueia Categorias e Campos para quem não é super usuário.
- Detalhes, relatório (`intranet:acl-report`) e teste (`tests/acl/login-test.sh`): [docs/acl.md](docs/acl.md).
- O registro público de usuários fica desativado.

---

## 4. Template `hospital_intranet`

```
templates/hospital_intranet/
├── index.php  component.php  error.php  offline.php
├── templateDetails.xml          # posições, parâmetros (logo, nome, cores, imagem do hero)
├── joomla.asset.json            # Web Asset Manager: CSS/JS registrados e minificados
├── html/                        # overrides (seção 5)
└── language/pt-BR/
media/templates/site/hospital_intranet/
├── css/  variables.css  template.css  components.css  responsive.css
├── js/   navigation.js  greeting.js  table-filter.js  calendar.js
└── images/ logo.svg  hero.webp  icons.svg (sprite SVG)
```

- **Referência visual** (imagem recebida):
  - O header não é uma barra sólida: fica **sobre o hero**, transparente, com uma linha fina separando-o do texto. Logo à esquerda (ícone branco arredondado + "HOSPITAL / SANTA AURORA"); menu à direita, com sublinhado no item ativo.
  - Hero com **foto do prédio sob sobreposição azul-petróleo**; o rótulo "INTRANET CORPORATIVA" fica em verde-água, espaçado e em caixa alta.
  - O **painel de acesso rápido é um card branco grande que sobrepõe a base do hero**, com uma grade 3×2 de itens em formato de lista: ícone num quadrado de cantos arredondados com fundo pastel (cor diferente por sistema), título, descrição e chevron `›`.
  - Cada seção abre com um rótulo pequeno (verde-água, caixa alta, espaçado), seguido de um título ("Últimas notícias") e, à direita, o link "Ver todas ›".
  - Os cards de notícia têm foto no topo e cantos arredondados. O fundo da página é cinza-esverdeado muito claro.
  - Conteúdo centralizado com largura máxima de ~1120 px.
- **Posições:** `header`, `mainmenu`, `search`, `hero`, `alerts`, `quick-access`, `news`, `protocols`, `events`, `sidebar`, `main-top`, `main-bottom`, `footer`, `footer-menu`.
- **CSS próprio com design tokens** (as variáveis da §3 da especificação), sem Bootstrap no frontend. O Joomla 6 inclui Bootstrap, mas o template não o carrega, para ficar leve e sem "cara de Joomla".
- **Ícones:** sprite SVG com os ícones usados de **Lucide** (licença ISC; traço fino, igual à imagem de referência), gerado por `scripts/build-icons.sh` a partir de `scripts/icons.txt`. O campo "ícone" dos sistemas usa os nomes Lucide (`users`, `phone`…).
- **Fontes** servidas localmente (sem CDN externa), já que a intranet pode não ter internet.
- **Layout:** header de 64–72 px em azul-petróleo; menu horizontal com indicador no item ativo; hambúrguer no mobile com `aria-expanded`. Grade de cards com 3, 2 e 1 colunas.
- **Acessibilidade:** link "pular para o conteúdo", foco visível, hierarquia de títulos, `prefers-reduced-motion`, contraste AA verificado com axe.
- **Design system** (§29): componentes CSS `btn`, `card`, `badge`, `alert`, `input`, `search`, `table`, `news-card`, `system-card`, `document-card`, `event-card`, `protocol-list`, com os estados default / hover / focus / active / disabled / loading. Uma página de amostra (`/design-system`, oculta do menu) mostra todos eles.

---

## 5. Módulos e páginas

Seguindo a regra "nativo primeiro", a maior parte dos `mod_hospital_*` da §25 vira **layout override** de módulo nativo. Código novo só onde o nativo não atende:

| Item da spec | Implementação |
|---|---|
| `mod_hospital_quick_access` | `mod_articles` (categoria Sistemas, só Destaques, ordem dos destaques) + override `quickaccess.php`, com a faixa "Ramais" (§13.9) |
| `mod_hospital_news` | `mod_articles` (Notícias, 3 itens) + override `newscards.php` |
| `mod_hospital_protocols` | `mod_articles` (Biblioteca, mais recentes) + override `protocols.php` |
| `mod_hospital_alerts` | `mod_articles` (Avisos, respeitando início/fim de publicação) + override `alerts.php`, ordenado por prioridade |
| `mod_hospital_search` | `mod_finder` (Smart Search) no header + override |
| `mod_hospital_events` | **Módulo próprio**: o Joomla não ordena nem filtra artigos por campo personalizado (data do evento). Tem dois layouts: "próximos eventos" (home) e "calendário mensal + lista" (página Eventos) |
| `mod_hospital_directory` | Página do `com_ramais` + card "Ramais" no acesso rápido (é um artigo de Sistemas apontando para `/ramais`) |

**Páginas (Menu Manager):**

| Item | Tipo |
|---|---|
| Início | Página inicial com os módulos na ordem da §36: hero (saudação + **avisos**, visíveis sem rolar) → acesso rápido → notícias → protocolos → eventos (fase 6) → footer |
| Sistemas | Categoria Sistemas + override: grupos por subcategoria, cards "Acessar sistema →" |
| Ramais | `com_ramais`: tabela com `<th scope="col">` ordenável pelo cabeçalho, busca em tempo real (JS) e fallback `?q=` sem JS; vira cards no mobile |
| Eventos | Página com `mod_hospital_events` no layout de calendário |
| Protocolos | Biblioteca filtrada (Protocolos + POPs) |
| Notícias | Blog da categoria Notícias + filtro por subcategoria |
| Documentos | Lista da Biblioteca + override com filtro por tipo/setor e busca por código/título |
| Resultados da busca | `com_finder` + override agrupando por tipo ("Documentos — 3 resultados") |

**Busca global:** o Smart Search indexa todos os artigos (notícias, documentos, sistemas, eventos, avisos). Um plugin `plg_finder_ramais` indexa os ramais.

---

## 6. Estrutura do repositório

```
hospital-intranet/
├── docker-compose.yml   .env.example   README.md
├── docker/
│   └── apache-intranet.conf          # restrição de IP, headers de segurança
├── src/                              # código-fonte das extensões
│   ├── templates/hospital_intranet/
│   ├── media/templates/site/hospital_intranet/
│   ├── components/com_ramais/        # admin + site + sql + media
│   ├── modules/mod_hospital_events/
│   ├── plugins/finder/ramais/
│   ├── plugins/console/intranet/     # comando `intranet:setup` (seed)
│   └── pkg_hospital_intranet.xml     # pacote que instala tudo junto
├── scripts/
│   ├── build.sh                      # gera o .zip do pacote
│   ├── install.sh                    # sobe containers, instala pacote, roda setup
│   └── backup.sh                     # mysqldump + arquivos (cron)
└── docs/
    ├── instalacao.md
    ├── configuracao.md
    ├── manual-administrador.md       # por papel: o que cada um faz e como
    └── acl.md
```

**Seed idempotente** (`php cli/joomla.php intranet:setup [--demo]`): cria categorias, campos personalizados, grupos, permissões por categoria, menus, módulos nas posições e, com `--demo`, o conteúdo da §31 (3 notícias, 4 protocolos, 7 sistemas incluindo Ramais, eventos, avisos e cerca de 20 ramais). Assim uma instalação nova fica reproduzível sem clicar no backend.

---

## 7. Fases de entrega

| # | Fase | Entregável verificável |
|---|---|---|
| 0 | **Infra** ✅ | `scripts/install.sh` sobe o Joomla instalado automaticamente em `http://localhost:8080` |
| 1 | **Template base** ✅ | Header, menu responsivo, hero com saudação, footer, tokens, página de design system (`/?tmpl=designsystem`), comando `intranet:setup` (template, categorias, menus, módulos) |
| 2 | **Modelo de conteúdo + ACL** ✅ | Comando `intranet:setup`: categorias, campos, grupos, permissões, menus |
| 3 | **Home** ✅ | Overrides: acesso rápido, notícias, protocolos, avisos |
| 4 | **Ramais** | `com_ramais` (admin + frontend + busca + ordenação + mobile) |
| 5 | **Sistemas, Documentos, Protocolos, Notícias** | Páginas internas com filtros |
| 6 | **Eventos** | `mod_hospital_events` (próximos + calendário mensal) |
| 7 | **Busca global** | Smart Search + `plg_finder_ramais` + página de resultados agrupada |
| 8 | **Conteúdo demo** (parcial ✅) | `intranet:setup --demo`: sistemas, notícias, documentos, aviso e eventos já existem; faltam ramais (fase 4) |
| 9 | **Segurança e operação** | Restrição de IP, MFA no admin, tipos de upload (pdf, docx, xlsx, pptx, jpg, png, webp), headers HTTP, Action Logs, política de senhas, backup |
| 10 | **Documentação** | `docs/` completo, pacote `.zip` instalável em qualquer Joomla 6 |

**Verificação em cada fase:** abrir as páginas no container em três larguras (1280, 768 e 375 px), rodar axe para acessibilidade e testar cada papel logando no backend para confirmar que ele só vê o que deve.

---

## 8. Fora do escopo (preparado, não implementado)

LDAP/AD, Entra ID, HIS, ERP, RH, chamados, BI (Metabase, Superset, Grafana), calendário, e-mail e chat (§28). Os links são configuráveis pelos artigos de Sistemas. LDAP e SSO entram depois pelos plugins de autenticação do Joomla, só para o backend, sem mudar o template.

---

## 9. Decisões confirmadas

- Imagem de referência recebida (seção 4).
- Logo provisório até o definitivo.
- A Imprensa publica Avisos.

## 10. Pendências

1. **Rede** — confirmar a faixa de IPs internos para a restrição de acesso (fase 9).
