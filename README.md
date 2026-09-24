# Intranet Hospital Santa Aurora

Intranet corporativa em **Joomla 6** (PHP 8.3, MariaDB 11.4) rodando em Docker.
Plano do projeto e decisões: [PLANO.md](PLANO.md).

## Requisitos

- Docker com Compose v2
- `curl` e acesso a `update.joomla.org` na primeira instalação (download do idioma pt-BR).
  Sem internet, coloque o pacote pt-BR em `var/packages/vendor/pt-BR.zip`.

## Instalação

```bash
scripts/install.sh
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
scripts/reset.sh                 # apaga banco e arquivos e reinstala do zero (pede confirmação)
scripts/visual-check.sh          # screenshots desktop/tablet/mobile + axe (acessibilidade) em var/visual/
scripts/build-icons.sh           # regenera o sprite de ícones a partir de scripts/icons.txt
docker compose exec -u www-data joomla php cli/joomla.php intranet:setup   # recria o que faltar
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
  plugins/console/intranet/                  comando intranet:setup
scripts/                      install, reset, visual-check, build-icons
tests/visual/                 script de screenshots/acessibilidade
var/                          pacotes baixados, screenshots (ignorado)
```

## Design system

Catálogo de componentes e estados: http://localhost:8080/?tmpl=designsystem.
Tokens (cores, fontes, espaçamentos) ficam em `src/media/templates/site/hospital_intranet/css/variables.css`.

Registros criados pelo `intranet:setup` têm a nota `intranet:<chave>` no painel. **Não altere essas notas**: é por elas que o setup reconhece o que já existe.
