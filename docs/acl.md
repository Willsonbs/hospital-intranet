# Papéis e permissões

O site da intranet **não tem login**: qualquer colaborador na rede interna vê o conteúdo publicado.
O login existe só no painel (`/administrator`), para três papéis:

| Papel (grupo no Joomla) | O que faz |
|---|---|
| **Administrador** | Tudo: configuração, usuários, menus, módulos, categorias, campos, ramais, sistemas e todo o conteúdo. |
| **Qualidade e Educação Permanente** | Cria, edita e publica documentos na **Biblioteca** (protocolos, POPs, manuais…) e envia arquivos pelo Gerenciador de Mídia. |
| **Imprensa** | Cria, edita e publica **Notícias**, **Eventos** e **Avisos** e envia imagens pelo Gerenciador de Mídia. |

Regras gerais (menor privilégio):

- Qualidade e Imprensa **não excluem** conteúdo nem arquivos. Eles podem despublicar ou mandar para a lixeira, e o Administrador esvazia a lixeira.
- Qualidade e Imprensa **não alteram categorias nem a estrutura dos campos**. O ACL do Joomla não separa "editar artigos da categoria" de "editar a categoria", então o plugin *Sistema - Intranet* bloqueia essas duas telas para quem não é super usuário. **Mantenha esse plugin ativado.**
- Ramais, Sistemas, Menus, Módulos, Usuários e Configuração ficam só com o Administrador.
- Não crie contas para os colaboradores: o site não precisa de login.

## Conferir as permissões

```bash
docker compose exec -u www-data joomla php cli/joomla.php intranet:acl-report
```

O comando mostra a matriz papel × permissão calculada pelo próprio Joomla, com heranças e negações.
Para testar pelo navegador, com logins reais de cada papel:

```bash
tests/acl/login-test.sh
```

## Criar um usuário do painel

Em **Usuários → Gerenciar → Novo**, marque **um** dos grupos acima.

- A senha precisa ter 12 ou mais caracteres, com maiúscula, minúscula, número e símbolo.
- O cadastro público está desativado.

## Conceder mais permissões depois

Faça isso pelo ACL nativo, sem mexer em código.

**Exemplo 1:** permitir que a Imprensa edite também a Biblioteca.
1. Abra **Conteúdo → Categorias → Biblioteca → Permissões**.
2. No grupo *Imprensa*, marque *Criar*, *Editar*, *Editar próprio* e *Editar estado* como **Permitido**.

**Exemplo 2:** permitir que a Qualidade exclua definitivamente documentos da Biblioteca.
1. Na mesma aba de permissões, no grupo *Qualidade e Educação Permanente*, marque *Excluir* como **Permitido**.

O comando `intranet:setup` só preenche permissões que ainda não estão definidas. Um ajuste feito no painel, inclusive **Negado**, não é desfeito quando o setup roda de novo.

## Campos de cada tipo de conteúdo

Os campos aparecem no formulário do artigo conforme a categoria escolhida.

| Categoria | Campos |
|---|---|
| Biblioteca (e subcategorias) | Código, Versão, Status, Setor responsável, Data de revisão, Público-alvo, Arquivo |
| Sistemas (e subcategorias) | Endereço (URL), Ícone, Cor do ícone, Abrir em nova aba. O **acesso rápido** da página inicial mostra os sistemas marcados como **Destaque** (estrela), na ordem de *Conteúdo → Artigos em destaque*. |
| Eventos | Início, Término, Local, Responsável, Link |
| Avisos | Prioridade, Público, Setor. O período de exibição é o **Início/Fim da publicação** do próprio artigo. |
| Notícias | nenhum. Usam imagem de introdução, categoria e tags do Joomla. |

As listas de setores e públicos podem receber novas opções em **Conteúdo → Campos**, só pelo Administrador.
