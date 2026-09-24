# Prompt --- Intranet Hospitalar com Joomla

## Objetivo

Desenvolver uma intranet corporativa moderna para um hospital,
utilizando o **Joomla como CMS**, com interface responsiva, acessível e
otimizada para computadores, tablets e dispositivos móveis.

A interface deverá utilizar como principal referência visual a imagem
fornecida pelo usuário.

O resultado deve transmitir:

-   confiança
-   organização
-   segurança
-   modernidade
-   ambiente hospitalar
-   facilidade de acesso às informações
-   baixa complexidade visual
-   navegação rápida

A intranet será o principal portal interno de informações e serviços do
hospital.

------------------------------------------------------------------------

# 1. Tecnologias

Utilizar:

-   Joomla como CMS
-   Template Joomla customizado
-   HTML5
-   CSS3
-   JavaScript moderno
-   Bootstrap 5 ou CSS customizado equivalente
-   Font Awesome ou biblioteca de ícones compatível
-   Joomla Custom Fields
-   Joomla Categories
-   Joomla Articles
-   Joomla Media Manager
-   Joomla ACL
-   Joomla Search
-   Joomla User Management

Sempre que possível, utilizar recursos nativos do Joomla antes de criar
funcionalidades customizadas.

A arquitetura deve permitir futura criação de componentes, módulos e
plugins personalizados.

------------------------------------------------------------------------

# 2. Referência visual

Utilizar a imagem fornecida como referência visual principal.

A página inicial deverá seguir aproximadamente a seguinte composição:

``` text
┌─────────────────────────────────────────────┐
│ LOGO                    MENU SUPERIOR       │
│                                             │
│ INTRANET CORPORATIVA                        │
│ Bom dia!                                    │
│ Tudo o que você precisa...                  │
│                                             │
├─────────────────────────────────────────────┤
│ ACESSO RÁPIDO                               │
│                                             │
│ [Portal] [Sistema] [Indicadores]             │
│ [Agenda ] [Chamados] [Documentos]            │
│                                             │
├─────────────────────────────────────────────┤
│ FIQUE POR DENTRO                            │
│ Últimas notícias                             │
│                                             │
│ [ CARD ] [ CARD ] [ CARD ]                  │
│                                             │
├─────────────────────────────────────────────┤
│ BIBLIOTECA INSTITUCIONAL                    │
│ Últimos protocolos adicionados               │
│                                             │
│ [ protocolo ]                               │
│ [ protocolo ]                               │
│ [ protocolo ]                               │
│ [ protocolo ]                               │
└─────────────────────────────────────────────┘
```

Não copiar literalmente a identidade visual de outro site. Utilizar a
imagem somente como referência de estrutura, hierarquia, espaçamento e
experiência de usuário.

------------------------------------------------------------------------

# 3. Identidade visual

Criar uma identidade visual hospitalar moderna.

## Cores principais

Utilizar uma paleta semelhante à referência:

-   Azul-petróleo escuro
-   Azul institucional
-   Verde hospitalar
-   Branco
-   Cinza muito claro
-   Cinza médio
-   Verde-água para destaques

Exemplo:

``` css
--primary: #075B63;
--primary-dark: #064850;
--primary-light: #0B7378;

--secondary: #E9F6F5;

--background: #F4F7F7;

--surface: #FFFFFF;

--text-primary: #102A2E;
--text-secondary: #607477;

--border: #DCE7E8;

--success: #2E9B76;
--warning: #E8A43A;
--danger: #D95C5C;
```

Não utilizar cores excessivamente saturadas.

O design deve ser limpo e institucional.

------------------------------------------------------------------------

# 4. Header

Criar um header horizontal.

Desktop:

``` text
[LOGO]                           Início
                                 Sistemas ▾
                                 Ramais
                                 Eventos
                                 Protocolos
```

Características:

-   altura aproximada de 64--72px
-   fundo azul-petróleo
-   logo do hospital à esquerda
-   menu à direita
-   textos pequenos e elegantes
-   item ativo com indicador inferior
-   navegação responsiva

Menu:

-   Início
-   Sistemas
-   Ramais
-   Eventos
-   Protocolos
-   Notícias
-   Documentos

Adicionar menu do usuário no canto direito:

``` text
[avatar] João da Silva ▾
```

Dropdown:

-   Meu perfil
-   Alterar senha
-   Preferências
-   Sair

------------------------------------------------------------------------

# 5. Hero / Boas-vindas

Criar uma área de boas-vindas.

Exemplo:

``` text
INTRANET CORPORATIVA

Bom dia!

Tudo o que você precisa para cuidar de quem importa,
em um só lugar.
```

Utilizar fundo azul-petróleo com textura/imagem hospitalar discreta.

O fundo não deve prejudicar a leitura.

Adicionar saudação dinâmica:

``` text
Bom dia!
Boa tarde!
Boa noite!
```

baseada no horário.

Caso o usuário esteja autenticado:

``` text
Bom dia, João!
```

------------------------------------------------------------------------

# 6. Acesso rápido

Criar uma seção chamada:

**ACESSO RÁPIDO**

**Sistemas e ferramentas**

Criar cards para os principais sistemas.

### Portal do Colaborador

Descrição:

> Holerites, benefícios e dados cadastrais

Ícone de usuário.

### Sistema Assistencial

Descrição:

> Prontuário e segurança de pacientes

Ícone hospitalar.

### Indicadores

Descrição:

> Painéis e resultados institucionais

Ícone de gráfico.

### Agenda de Salas

Descrição:

> Reservas de espaços e equipamentos

Ícone calendário.

### Chamados de TI

Descrição:

> Suporte técnico e acompanhamento

Ícone computador.

### Documentos

Descrição:

> Protocolos, manuais e formulários

Ícone documento.

Cada card deve possuir:

-   ícone
-   título
-   descrição
-   seta
-   efeito hover
-   cursor pointer

------------------------------------------------------------------------

### Diretório de Ramais

Descrição:

> Consulte rapidamente nomes, setores, unidades e ramais telefônicos.

Ícone sugerido:

`fa-phone`

Destino:

`/ramais`

---

# 7. Gerenciamento dos acessos rápidos

Os acessos rápidos **não devem ser codificados diretamente no
template**.

Criar uma solução administrável pelo Joomla.

Cada acesso deve possuir:

-   título
-   descrição
-   ícone
-   URL
-   categoria
-   ordem
-   ativo/inativo
-   abrir em nova aba
-   permissões de acesso

Exemplo:

``` text
Portal do Colaborador
Descrição:
Holerites, benefícios e dados cadastrais

URL:
https://portal.exemplo.local

Ícone:
fa-user

Publicado:
Sim

Ordem:
1
```

Administradores poderão criar novos atalhos diretamente no Joomla.

------------------------------------------------------------------------

# 8. Notícias

Criar seção:

**FIQUE POR DENTRO**

**Últimas notícias**

Exibir inicialmente 3 cards.

Cada card deverá possuir:

-   imagem
-   categoria
-   data
-   título
-   resumo
-   botão "Leia mais"

Exemplo:

``` text
┌───────────────────────┐
│                       │
│       IMAGEM          │
│                       │
├───────────────────────┤
│ INSTITUCIONAL         │
│ 12 JUN 2026           │
│                       │
│ Novo comunicado...    │
│                       │
│ Saiba mais →          │
└───────────────────────┘
```

Utilizar:

-   Joomla Articles
-   Joomla Categories
-   Joomla Tags
-   Featured Articles

------------------------------------------------------------------------

# 9. Categorias de notícias

Criar categorias:

-   Institucional
-   Pessoas
-   Saúde e Bem-estar
-   Tecnologia
-   Recursos Humanos
-   Eventos
-   Comunicados
-   TI

Permitir filtros.

------------------------------------------------------------------------

# 10. Protocolos e documentos

Criar uma seção:

**BIBLIOTECA INSTITUCIONAL**

**Últimos protocolos adicionados**

Mostrar os últimos documentos adicionados.

Exemplo:

``` text
┌─────────────────────────────────────────────┐
│ 📄  POP-ENF-042                             │
│     Administração segura de medicamentos    │
│                                             │
│     Enfermagem                              │
│                           Versão 4.2    ›   │
├─────────────────────────────────────────────┤
│ 📄  PRT-CCIH-018                            │
│     Precauções e isolamento hospitalar      │
│                                             │
│     Controle de Infecção     Versão 3.0 ›   │
└─────────────────────────────────────────────┘
```

------------------------------------------------------------------------

# 11. Repositório de documentos

Criar uma biblioteca documental administrável pelo Joomla.

Tipos:

-   Protocolos
-   POPs
-   Manuais
-   Formulários
-   Políticas
-   Normas
-   Fluxogramas
-   Treinamentos
-   Documentos de RH
-   Documentos de TI

Cada documento deverá possuir:

-   código
-   título
-   descrição
-   categoria
-   setor responsável
-   versão
-   data de publicação
-   data de revisão
-   arquivo
-   autor
-   status
-   público-alvo

------------------------------------------------------------------------

# 12. Controle de acesso

Utilizar o sistema ACL do Joomla.

Criar grupos:

-   Super Administrador
-   Administrador da Intranet
-   TI
-   RH
-   Enfermagem
-   Corpo Clínico
-   Gestão
-   Colaborador

Exemplo:

``` text
Documento público
→ todos os colaboradores

Documento RH
→ colaboradores + RH

Documento Gestão
→ gestão

Documento TI
→ TI
```

Não permitir que usuários comuns alterem documentos.

------------------------------------------------------------------------

# 13. Diretório de ramais

Criar uma página específica no menu principal chamada:

**RAMAIS**

O acesso deverá estar disponível no menu superior da intranet.

A página deverá apresentar um diretório telefônico institucional simples, rápido e responsivo, permitindo que o colaborador localize facilmente o ramal de cada setor.

## 13.1. Estrutura da tabela

A tabela deverá possuir **exatamente três colunas**:

| Setor | Ramal | Localização |
|---|---:|---|
| Recepção | 2010 | Térreo |
| Tecnologia da Informação | 2045 | 1º andar |
| Enfermagem | 2080 | 2º andar |
| Recursos Humanos | 2090 | Prédio Administrativo |
| Financeiro | 2100 | Prédio Administrativo |

### Campos

#### Setor

Nome do setor responsável pelo ramal.

Exemplos:

- Recepção
- Tecnologia da Informação
- Enfermagem
- Recursos Humanos
- Financeiro
- Farmácia
- Faturamento
- Centro Cirúrgico
- UTI
- Almoxarifado

#### Ramal

Número do ramal telefônico.

Exemplos:

```text
2010
2045
2080
2090
2100
```

#### Localização

Local físico onde o setor/ramal está localizado.

Exemplos:

```text
Térreo
1º andar
2º andar
Prédio Administrativo
Centro Cirúrgico
UTI
Ambulatório
```

## 13.2. Pesquisa

Adicionar campo de pesquisa no topo:

```text
┌─────────────────────────────────────────────────────┐
│ 🔎 Pesquisar setor, ramal ou localização...        │
└─────────────────────────────────────────────────────┘
```

A pesquisa deverá permitir localizar registros por:

- setor
- ramal
- localização

A busca deverá funcionar em tempo real ou ao pressionar Enter.

## 13.3. Ordenação

Permitir ordenar a tabela por:

- Setor
- Ramal
- Localização

A ordenação padrão deverá ser por **Setor em ordem alfabética**.

## 13.4. Layout desktop

Utilizar uma tabela limpa e institucional:

```text
┌────────────────────────────┬────────┬─────────────────────────┐
│ SETOR                      │ RAMAL  │ LOCALIZAÇÃO             │
├────────────────────────────┼────────┼─────────────────────────┤
│ Recepção                   │ 2010   │ Térreo                  │
│ Tecnologia da Informação   │ 2045   │ 1º andar                │
│ Enfermagem                 │ 2080   │ 2º andar                │
│ Recursos Humanos           │ 2090   │ Prédio Administrativo   │
│ Financeiro                 │ 2100   │ Prédio Administrativo   │
└────────────────────────────┴────────┴─────────────────────────┘
```

Características:

- fundo branco
- bordas discretas
- cabeçalho em azul-petróleo
- linhas com espaçamento confortável
- efeito hover
- tipografia legível
- boa separação visual entre registros

## 13.5. Layout mobile

No celular, transformar a tabela em cards:

```text
┌───────────────────────────────┐
│ RECEPÇÃO                      │
│                               │
│ Ramal                         │
│ 2010                          │
│                               │
│ Localização                   │
│ Térreo                        │
└───────────────────────────────┘

┌───────────────────────────────┐
│ TECNOLOGIA DA INFORMAÇÃO      │
│                               │
│ Ramal                         │
│ 2045                          │
│                               │
│ Localização                   │
│ 1º andar                      │
└───────────────────────────────┘
```

## 13.6. Cadastro administrativo

Os registros deverão ser administráveis pelo Joomla.

Cada registro deverá possuir somente os campos funcionais necessários ao diretório:

```text
Setor
Ramal
Localização
Status
Ordem
```

O campo **Status** deverá permitir:

```text
Ativo
Inativo
```

Registros inativos não deverão aparecer no frontend.

O campo **Ordem** poderá ser utilizado para definir uma ordenação personalizada quando necessário.

## 13.7. Permissões

O gerenciamento dos ramais deverá ser realizado pelo painel administrativo do Joomla.

O papel **Administrador** terá acesso completo.

Os papéis **Qualidade e Educação Permanente** e **Imprensa** não deverão possuir permissão para alterar os ramais, salvo se essa permissão for posteriormente concedida pelo Administrador através do ACL.

## 13.8. Menu principal

Adicionar ao menu principal:

```text
Início
Sistemas
Ramais
Eventos
Protocolos
Notícias
Documentos
```

Ao clicar em **Ramais**, abrir a página do diretório.

O item **Ramais** deverá permanecer visualmente destacado enquanto o usuário estiver nessa página.

## 13.9. Acesso rápido

Na homepage, incluir um card:

```text
☎ Ramais

Consulte rapidamente o ramal
e a localização dos setores.

[Consultar ramais →]
```

O card deverá apontar para a página de ramais.

## 13.10. Acessibilidade

A tabela deverá possuir:

- cabeçalhos `<th>` corretamente identificados
- associação semântica entre cabeçalho e células
- navegação por teclado
- foco visível
- contraste adequado
- versão responsiva
- textos legíveis em dispositivos móveis

Não utilizar somente cor para diferenciar registros.

# 14. Sistemas

Criar página:

**SISTEMAS E FERRAMENTAS**

Organizar por categorias:

-   Assistenciais
-   Administrativos
-   Recursos Humanos
-   Financeiro
-   TI
-   BI e Indicadores

Cada sistema:

``` text
[Ícone]

Nome
Descrição

[Acessar sistema →]
```

------------------------------------------------------------------------

# 15. Eventos

Criar página:

**EVENTOS**

Visualização:

-   calendário mensal
-   lista de eventos
-   eventos futuros

Cada evento deverá possuir:

-   título
-   data
-   hora
-   local
-   descrição
-   responsável
-   link

Exemplo:

``` text
15
JUN

Treinamento de Segurança do Paciente

14:00
Auditório Principal
```

------------------------------------------------------------------------

# 16. Busca global

Adicionar busca global no header.

Exemplo:

``` text
🔎 Pesquisar na intranet...
```

A busca deverá pesquisar:

-   notícias
-   documentos
-   protocolos
-   sistemas
-   ramais
-   eventos

Criar página de resultados:

``` text
Resultados para: segurança

Documentos
3 resultados

Notícias
2 resultados

Protocolos
5 resultados
```

------------------------------------------------------------------------

# 17. Dashboard

Após login, permitir que o usuário visualize:

``` text
Olá, João!

Acesso rápido

Meus sistemas

Últimas notícias

Próximos eventos

Documentos recentes

Avisos importantes
```

------------------------------------------------------------------------

# 18. Avisos importantes

Criar componente para comunicados institucionais.

Exemplo:

``` text
⚠ AVISO IMPORTANTE

Manutenção programada do sistema assistencial

Hoje, das 22h às 23h.

[Saiba mais]
```

Permitir ao administrador definir:

-   prioridade
-   período de exibição
-   público
-   setor
-   data de início
-   data de término

------------------------------------------------------------------------

# 19. Footer

Criar footer institucional:

``` text
HOSPITAL SANTA AURORA

Intranet Corporativa

Links úteis
Sistemas
Ramais
Documentos
Eventos

TI Hospitalar
Suporte: ramal 2000

© 2026 Hospital Santa Aurora
```

------------------------------------------------------------------------

# 20. Responsividade

A interface deve ser:

## Desktop

Menu horizontal.

Cards em 3 colunas.

## Tablet

Cards em 2 colunas.

## Mobile

Menu hamburger.

Cards em uma coluna.

Exemplo:

``` text
Desktop:

[ CARD ][ CARD ][ CARD ]

Tablet:

[ CARD ][ CARD ]
[ CARD ][ CARD ]

Mobile:

[ CARD ]
[ CARD ]
[ CARD ]
```

------------------------------------------------------------------------

# 21. Acessibilidade

Seguir boas práticas WCAG.

Implementar:

-   contraste adequado
-   navegação por teclado
-   foco visível
-   labels
-   aria-label
-   textos alternativos
-   estrutura semântica HTML
-   headings hierárquicos
-   botões acessíveis
-   links identificáveis
-   suporte a leitores de tela

Não utilizar informação somente por cor.

------------------------------------------------------------------------

# 22. Performance

O template deverá ser otimizado.

Utilizar:

-   CSS minificado
-   JavaScript modular
-   lazy loading de imagens
-   WebP
-   SVG para ícones
-   carregamento assíncrono quando possível
-   evitar bibliotecas desnecessárias

------------------------------------------------------------------------

# 23. Estrutura do template Joomla

Criar um template customizado chamado:

`hospital_intranet`

Estrutura sugerida:

``` text
/templates/hospital_intranet/

├── index.php
├── component.php
├── error.php
├── offline.php
├── templateDetails.xml
│
├── css/
│   ├── template.css
│   ├── variables.css
│   ├── components.css
│   └── responsive.css
│
├── js/
│   ├── template.js
│   ├── navigation.js
│   └── search.js
│
├── images/
│   ├── logo.svg
│   ├── hero.webp
│   └── placeholders/
│
├── html/
│   ├── com_content/
│   ├── mod_menu/
│   └── mod_custom/
│
└── language/
    └── pt-BR/
```

------------------------------------------------------------------------

# 24. Posições do template

Criar posições Joomla:

``` text
header
mainmenu
hero
quick-access
alerts
news
protocols
events
sidebar
footer
footer-menu
```

Exemplo:

``` php
<jdoc:include type="modules" name="quick-access" />
```

------------------------------------------------------------------------

# 25. Componentes / módulos

Criar módulos reutilizáveis:

``` text
mod_hospital_quick_access
mod_hospital_news
mod_hospital_protocols
mod_hospital_events
mod_hospital_alerts
mod_hospital_search
mod_hospital_directory
```

Sempre que possível, utilizar Joomla Modules e Articles antes de
desenvolver componentes customizados.

------------------------------------------------------------------------

# 26. Administração do Joomla

O painel administrativo deverá ser organizado de forma que cada papel visualize apenas as funcionalidades necessárias.

O administrador deverá conseguir administrar:

### Notícias

Criar / editar / publicar / arquivar.

### Documentos

Upload / versão / categoria / permissões.

### Sistemas

Cadastrar sistemas e links.

### Ramais

Cadastrar colaboradores e setores.

### Eventos

Cadastrar eventos.

### Avisos

Criar comunicados temporários.

### Usuários administrativos

Gerenciar somente os usuários que possuem acesso ao backend do Joomla.

Os papéis administrativos previstos são:

- Administrador
- Qualidade e Educação Permanente
- Imprensa

Não criar usuários Joomla para os colaboradores apenas para acesso à intranet.

------------------------------------------------------------------------

# 27. Segurança

A intranet será utilizada em ambiente hospitalar.

Não armazenar dados clínicos sensíveis diretamente no CMS.

O Joomla deverá funcionar como portal institucional e camada de acesso
aos sistemas.

Implementar:

-   HTTPS
-   autenticação
-   ACL
-   proteção contra CSRF
-   proteção XSS
-   validação de uploads
-   limite de tipos de arquivo
-   controle de permissões
-   logs
-   backup
-   atualização do Joomla
-   política de senhas
-   sessão segura

Não exibir informações de pacientes na página pública da intranet.

------------------------------------------------------------------------

# 28. Integrações futuras

Preparar arquitetura para integração futura com:

-   Active Directory / LDAP
-   Microsoft Entra ID
-   Sistema hospitalar/HIS
-   ERP
-   Sistema de RH
-   Sistema de chamados
-   Metabase
-   Apache Superset
-   Grafana
-   Calendário corporativo
-   E-mail institucional
-   Openfire / chat corporativo

Os links para sistemas externos devem ser configuráveis pelo
administrador.

------------------------------------------------------------------------

# 29. Design system

Criar componentes reutilizáveis:

``` text
Button
Card
Badge
Alert
Modal
Dropdown
Input
Search
Table
DocumentCard
NewsCard
SystemCard
EventCard
ProtocolList
```

Todos devem possuir estados:

``` text
default
hover
focus
active
disabled
loading
```

------------------------------------------------------------------------

# 30. Experiência do usuário

A página inicial deve permitir que o colaborador encontre rapidamente:

1.  Sistemas
2.  Documentos
3.  Ramais
4.  Notícias
5.  Eventos
6.  Comunicados

Evitar excesso de menus.

A navegação deve ser simples e intuitiva.

A intranet deve funcionar como uma:

> "Single Source of Truth"

para informações institucionais do hospital.

------------------------------------------------------------------------

# 31. Conteúdo demonstrativo

Criar dados fictícios para demonstração.

Hospital:

`Hospital Santa Aurora`

Notícias:

-   Hospital recebe certificação de qualidade nacional
-   Semana da Enfermagem celebra quem transforma o cuidado
-   Nova campanha incentiva hábitos saudáveis no trabalho

Protocolos:

-   POP-ENF-042 --- Administração segura de medicamentos
-   PRT-CCIH-018 --- Precauções e isolamento hospitalar
-   POP-FAR-027 --- Armazenamento de medicamentos termolábeis
-   PRT-SEG-005 --- Identificação correta do paciente

Sistemas:

-   Portal do Colaborador
-   Sistema Assistencial
-   Indicadores
-   Agenda de Salas
-   Chamados de TI
-   Documentos

------------------------------------------------------------------------

# 32. Modelo de acesso da intranet

A arquitetura de acesso deverá seguir este modelo:

```text
COLABORADOR
    │
    │ sem login
    ▼
┌─────────────────────────────┐
│     FRONTEND DA INTRANET    │
│                             │
│ Notícias                    │
│ Protocolos                  │
│ Documentos                  │
│ Eventos                     │
│ Ramais                      │
│ Sistemas                    │
│ Avisos                      │
└──────────────┬──────────────┘
               │
               │ somente links/conteúdo
               │ publicado
               ▼
┌─────────────────────────────┐
│      PAINEL JOOMLA          │
│       /administrator        │
└──────────────┬──────────────┘
               │
       ┌───────┼────────┐
       ▼       ▼        ▼
 Administrador Qualidade Imprensa
 Full Access  Documentos Notícias/
              Protocolos Eventos/
                         Imagens
```

### Regra principal

**Frontend:** sem login.

**Backend Joomla:** login obrigatório.

Somente usuários administrativos previamente cadastrados terão acesso ao `/administrator`.

# 36. Resultado esperado

Entregar um projeto Joomla funcional contendo:

1.  Template customizado
2.  Página inicial
3.  Menu principal
4.  Sistema de acesso rápido
5.  Notícias
6.  Documentos
7.  Protocolos
8.  Diretório de ramais
9.  Eventos
10. Avisos
11. Busca global
12. Sistema de usuários
13. ACL
14. Responsividade
15. Acessibilidade
16. Design system
17. Conteúdo demonstrativo
18. Estrutura preparada para integrações

A homepage deverá visualmente seguir a estrutura:

``` text
HEADER
   ↓
HERO / BOAS-VINDAS
   ↓
ACESSO RÁPIDO
   ↓
ÚLTIMAS NOTÍCIAS
   ↓
PROTOCOLOS RECENTES
   ↓
EVENTOS / AVISOS
   ↓
FOOTER
```

------------------------------------------------------------------------

# 33. Requisito importante

Não criar uma página estática.

Todo conteúdo que possa mudar deverá ser administrável pelo Joomla.

Por exemplo:

-   notícias → Joomla Articles
-   categorias → Joomla Categories
-   usuários → Joomla Users
-   permissões → Joomla ACL
-   imagens → Joomla Media
-   menus → Joomla Menu Manager
-   documentos → estrutura administrável
-   sistemas → módulo/componente administrável
-   eventos → componente/módulo administrável

O template deverá ser responsável principalmente pela apresentação
visual.

------------------------------------------------------------------------

# 34. Entrega

Fornecer:

1.  Estrutura completa do template Joomla
2.  `templateDetails.xml`
3.  `index.php`
4.  CSS
5.  JavaScript
6.  overrides Joomla
7.  módulos
8.  configuração de posições
9.  conteúdo de exemplo
10. categorias
11. ACL
12. instruções de instalação
13. instruções de configuração
14. documentação do administrador

Criar o projeto pensando em um ambiente real de hospital, com
possibilidade de expansão futura.

O resultado final deve ter aparência de um **portal corporativo
moderno**, e não de um site Joomla tradicional.

------------------------------------------------------------------------

# 35. Arquitetura recomendada

``` text
                    ┌─────────────────────┐
                    │       JOOMLA        │
                    │                     │
                    │  Template Custom    │
                    │  Hospital Intranet  │
                    └──────────┬──────────┘
                               │
             ┌─────────────────┼─────────────────┐
             │                 │                 │
             ▼                 ▼                 ▼
       ┌──────────┐      ┌──────────┐      ┌──────────┐
       │ Notícias │      │Documentos│      │ Eventos  │
       │ Articles │      │  Docs    │      │ Calendar │
       └──────────┘      └──────────┘      └──────────┘
             │                 │                 │
             └─────────────────┼─────────────────┘
                               ▼
                     ┌──────────────────┐
                     │       ACL        │
                     │ TI / RH / Gestão│
                     │ Colaboradores   │
                     └──────────────────┘
                               │
                               ▼
              ┌─────────────────────────────────┐
              │      SISTEMAS HOSPITALARES      │
              │                                 │
              │ HIS │ RH │ BI │ Chamados │ etc │
              └─────────────────────────────────┘
```

A arquitetura deve separar claramente:

**Template = apresentação**

**Módulos/componentes = funcionalidades**

**Joomla CMS = conteúdo e administração**

Isso permitirá que a intranet evolua futuramente para integrações com
LDAP/Active Directory, SSO, HIS hospitalar, chamados de TI, BI e
autenticação corporativa sem necessidade de reconstruir o portal.


---

# 37. Alterações incorporadas nesta versão

- O menu **Ramais** foi incluído no menu principal e na área de acesso rápido.
- O diretório possui exatamente as colunas **Setor, Ramal e Localização**.
- A busca permite consultar por setor, ramal ou localização.
- O cadastro dos ramais é administrado pelo Joomla e exibido somente para leitura no frontend.

Esta versão considera as seguintes decisões do projeto:

- Não haverá login no frontend da intranet.
- Colaboradores acessarão a intranet diretamente.
- O login ficará restrito ao painel administrativo do Joomla.
- Haverá três papéis administrativos:
  - Administrador — Full Access
  - Qualidade e Educação Permanente — documentos e protocolos
  - Imprensa — notícias, eventos e imagens
- As permissões deverão utilizar o ACL nativo do Joomla.
- O princípio do menor privilégio deverá ser aplicado aos papéis não administrativos.
- O conteúdo do frontend será gerenciado pelo backend do Joomla.
