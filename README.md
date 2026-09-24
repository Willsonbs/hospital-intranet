# Intranet Hospitalar — branch `feature/intranet-joomla`

Implementação paralela e **isolada** da intranet do Hospital Santa Aurora, feita a partir da
especificação [prompt_intranet_hospitalar_joomla.md](prompt_intranet_hospitalar_joomla.md) e da
referência visual [Hospital-Intranet-Page.png](Hospital-Intranet-Page.png).

> Esta branch não compartilha histórico com a `main`. O projeto principal
> (Joomla 6, estrutura `src/`) está na `main` do repositório
> [psysharq/hospital-intranet](https://github.com/psysharq/hospital-intranet) e não é alterado aqui.

## Stack

Joomla 5.4 (PHP 8.2, Apache), MariaDB 11.4 e phpMyAdmin em Docker.

## Subir o ambiente

```bash
cp .env.example .env        # troque as senhas
docker compose up -d
```

| | Endereço |
|---|---|
| Site | http://localhost:8080 |
| Administrador | http://localhost:8080/administrator |
| phpMyAdmin | http://localhost:8081 |

Depois da primeira subida, registre as extensões montadas e carregue o conteúdo de exemplo:

```bash
docker compose exec -u www-data joomla php cli/joomla.php extension:discover
docker compose exec -u www-data joomla php cli/joomla.php extension:discover:list
docker compose exec -u www-data joomla php cli/joomla.php extension:discover:install --eid=<id>
docker compose exec -T db mariadb -uintranet -p<senha> intranet < setup/seed/01_ramais.sql
docker compose cp setup/seed/02_conteudo.php joomla:/tmp/seed.php && docker compose exec -u www-data joomla php /tmp/seed.php
docker compose cp setup/seed/03_documentos.php joomla:/tmp/seed.php && docker compose exec -u www-data joomla php /tmp/seed.php
```

## Estrutura

```
templates/hospital_intranet/          template (layout, overrides, CSS, JS)
extensions/components/com_ramais/     diretório de ramais (seção 13)
extensions/components/com_documentos/ biblioteca de documentos e protocolos (seções 10 e 11)
extensions/modules/mod_hospital_quick_access/  acesso rápido e catálogo de sistemas (seções 6, 7 e 14)
extensions/modules/mod_hospital_protocols/     últimos protocolos na home (seção 10)
setup/seed/                           conteúdo inicial (idempotente)
```

## Situação

Feito: template, página inicial, menu principal, acesso rápido, sistemas, ramais, notícias,
documentos e protocolos, rodapé.
Pendente: eventos, avisos, busca global, papéis/ACL do painel, documentação do administrador.
