<?php

/**
 * Catálogo do design system: /?tmpl=designsystem
 * Referência visual dos componentes e seus estados para quem desenvolve
 * overrides e módulos. Não indexado por buscadores.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

require __DIR__ . '/partials/bootstrap.php';

$this->setMetaData('robots', 'noindex, nofollow');
$this->setTitle('Design system');

$icon = static fn (string $name, string $class = ''): string => LayoutHelper::render('hospital.icon', ['name' => $name, 'class' => $class]);

$systems = [
    ['Portal do Colaborador', 'Holerites, benefícios e dados cadastrais', 'users', 'blue'],
    ['Sistema Assistencial', 'Prontuário e segurança de pacientes', 'clipboard-list', 'green'],
    ['Indicadores', 'Painéis e resultados institucionais', 'chart-column', 'purple'],
    ['Agenda de Salas', 'Reservas de espaços e equipamentos', 'calendar', 'orange'],
    ['Chamados de TI', 'Suporte técnico e acompanhamento', 'monitor', 'slate'],
    ['Documentos', 'Protocolos, manuais e formulários', 'file-text', 'red'],
];

$swatches = [
    'Primária' => '--color-primary', 'Primária escura' => '--color-primary-dark', 'Primária clara' => '--color-primary-light',
    'Verde-água' => '--color-accent', 'Verde-água (texto)' => '--color-accent-strong', 'Secundária' => '--color-secondary',
    'Fundo' => '--color-bg', 'Superfície' => '--color-surface', 'Texto' => '--color-text', 'Texto secundário' => '--color-text-muted',
    'Borda' => '--color-border', 'Sucesso' => '--color-success', 'Atenção' => '--color-warning', 'Perigo' => '--color-danger',
];
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
    <style>
        .ds-section { margin-top: var(--space-12); }
        .ds-section > h2 { font-size: var(--font-size-xl); margin-bottom: var(--space-2); }
        .ds-section > p { color: var(--color-text-muted); max-width: 44rem; }
        .ds-row { display: flex; flex-wrap: wrap; gap: var(--space-3); align-items: center; margin-bottom: var(--space-4); }
        .ds-grid { display: grid; gap: var(--space-4); grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
        .ds-swatch { border-radius: var(--radius-lg); overflow: hidden; background: var(--color-surface); box-shadow: var(--shadow-sm); font-size: var(--font-size-xs); }
        .ds-swatch span { display: block; height: 64px; box-shadow: inset 0 -1px 0 var(--color-border); }
        .ds-swatch p { margin: 0; padding: var(--space-2) var(--space-3); }
        .ds-swatch code { color: var(--color-text-muted); }
        .ds-forms { display: grid; gap: var(--space-5); grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }
        .ds-state { font-size: var(--font-size-xs); color: var(--color-text-muted); margin: 0 0 var(--space-2); }
        .ds-media { display: grid; place-items: center; color: var(--tone-fg); background: var(--tone-bg); }
        .ds-media .icon { width: 40px; height: 40px; stroke-width: 1.5; }
    </style>
</head>
<body class="site is-inner is-designsystem">
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main id="conteudo" class="site-main" tabindex="-1">
        <div class="masthead">
            <div class="container page-banner">
                <p class="eyebrow eyebrow--on-dark">Referência</p>
                <h1 class="page-banner__title">Design system</h1>
            </div>
        </div>

        <div class="container site-main__content">

            <section class="ds-section" style="margin-top:0" aria-labelledby="ds-cores">
                <h2 id="ds-cores">Cores</h2>
                <p>Tokens definidos em <code>css/variables.css</code>. Nunca use valores de cor literais nos overrides.</p>
                <div class="ds-grid">
                    <?php foreach ($swatches as $label => $var) : ?>
                        <div class="ds-swatch"><span style="background: var(<?php echo $var; ?>)"></span><p><?php echo $label; ?><br><code><?php echo $var; ?></code></p></div>
                    <?php endforeach; ?>
                </div>
                <div class="ds-row" style="margin-top: var(--space-4)">
                    <?php foreach (['blue', 'green', 'purple', 'orange', 'slate', 'red', 'teal'] as $tone) : ?>
                        <span class="icon-tile tone-<?php echo $tone; ?>" title="tone-<?php echo $tone; ?>"><?php echo $icon('sparkles'); ?></span>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-tipo">
                <h2 id="ds-tipo">Tipografia</h2>
                <p>Figtree (servida localmente), pesos 300–900.</p>
                <p class="eyebrow">Eyebrow — rótulo de seção</p>
                <p style="font-size: var(--font-size-3xl); font-weight: 600; margin: 0; line-height: 1.1">Bom dia!</p>
                <p style="font-size: var(--font-size-2xl); font-weight: 600; margin: var(--space-2) 0 0">Título de página</p>
                <p style="font-size: 1.625rem; font-weight: 600; margin: var(--space-2) 0 0">Título de seção</p>
                <p style="font-size: var(--font-size-lg); font-weight: 600; margin: var(--space-2) 0 0">Título de card</p>
                <p style="margin: var(--space-2) 0 0">Texto corrido em 16px com altura de linha 1,55 para leitura confortável em qualquer tela.</p>
                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted)">Texto secundário em 14px.</p>
            </section>

            <section class="ds-section" aria-labelledby="ds-btn">
                <h2 id="ds-btn">Button</h2>
                <p>Estados: padrão, hover, foco (use Tab), ativo (ao clicar), desabilitado e carregando (<code>aria-busy="true"</code>).</p>
                <div class="ds-row">
                    <button class="btn btn--primary" type="button">Primário</button>
                    <button class="btn" type="button">Secundário</button>
                    <button class="btn btn--ghost" type="button">Discreto</button>
                    <button class="btn btn--danger" type="button">Perigo</button>
                    <button class="btn btn--primary" type="button"><?php echo $icon('download'); ?> Com ícone</button>
                    <button class="btn btn--sm" type="button">Pequeno</button>
                </div>
                <div class="ds-row">
                    <button class="btn btn--primary" type="button" disabled>Desabilitado</button>
                    <button class="btn" type="button" disabled>Desabilitado</button>
                    <button class="btn btn--primary" type="button" aria-busy="true">Carregando</button>
                    <button class="btn" type="button" aria-busy="true">Carregando</button>
                    <a class="link-arrow" href="#ds-btn">Ver todas <?php echo $icon('chevron-right'); ?></a>
                    <a class="link-arrow" href="#ds-btn">Leia mais <?php echo $icon('arrow-right'); ?></a>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-badge">
                <h2 id="ds-badge">Badge</h2>
                <div class="ds-row">
                    <span class="badge">Institucional</span>
                    <span class="badge badge--success"><?php echo $icon('circle-check'); ?> Vigente</span>
                    <span class="badge badge--warning"><?php echo $icon('clock'); ?> Em revisão</span>
                    <span class="badge badge--danger"><?php echo $icon('circle-x'); ?> Obsoleto</span>
                    <span class="badge badge--info">Novo</span>
                    <span class="badge badge--neutral">v4.2</span>
                    <span class="badge tone-purple">Tecnologia</span>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-alert">
                <h2 id="ds-alert">Alert</h2>
                <p>O tipo é indicado por ícone e rótulo, não só pela cor.</p>
                <div class="stack">
                    <div class="alert alert--warning" role="note">
                        <?php echo $icon('triangle-alert'); ?>
                        <div class="alert__body">
                            <span class="alert__label">Aviso importante</span>
                            <p class="alert__title">Manutenção programada do sistema assistencial</p>
                            <p class="alert__text">Hoje, das 22h às 23h.</p>
                            <a class="link-arrow" href="#ds-alert">Saiba mais <?php echo $icon('arrow-right'); ?></a>
                        </div>
                    </div>
                    <div class="alert" role="note"><?php echo $icon('info'); ?><div class="alert__body"><span class="alert__label">Informação</span><p class="alert__text">Novo fluxo de solicitação de materiais a partir de segunda-feira.</p></div></div>
                    <div class="alert alert--success" role="note"><?php echo $icon('circle-check'); ?><div class="alert__body"><span class="alert__label">Concluído</span><p class="alert__text">A migração do e-mail institucional foi concluída.</p></div></div>
                    <div class="alert alert--danger" role="note"><?php echo $icon('circle-x'); ?><div class="alert__body"><span class="alert__label">Crítico</span><p class="alert__text">Sistema de prescrição indisponível. Use o formulário de contingência.</p></div></div>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-form">
                <h2 id="ds-form">Input, Select e Search</h2>
                <div class="ds-forms">
                    <div class="field">
                        <label class="field__label" for="ds-i1">Padrão</label>
                        <input class="input" id="ds-i1" type="text" placeholder="Digite aqui">
                        <span class="field__hint">Texto de ajuda do campo.</span>
                    </div>
                    <div class="field">
                        <label class="field__label" for="ds-i2">Com erro</label>
                        <input class="input" id="ds-i2" type="text" value="abc" aria-invalid="true" aria-describedby="ds-i2-err">
                        <span class="field__error" id="ds-i2-err"><?php echo $icon('circle-x'); ?> Informe um ramal com 4 dígitos.</span>
                    </div>
                    <div class="field">
                        <label class="field__label" for="ds-i3">Desabilitado</label>
                        <input class="input" id="ds-i3" type="text" value="Não editável" disabled>
                    </div>
                    <div class="field">
                        <label class="field__label" for="ds-s1">Select</label>
                        <select class="select" id="ds-s1"><option>Todos os setores</option><option>Enfermagem</option><option>Farmácia</option></select>
                    </div>
                </div>
                <div class="stack" style="margin-top: var(--space-6)">
                    <div class="search-field" role="search">
                        <label class="sr-only" for="ds-q1">Pesquisar setor, ramal ou localização</label>
                        <?php echo $icon('search', 'search-field__icon'); ?>
                        <input class="input" id="ds-q1" type="search" placeholder="Pesquisar setor, ramal ou localização...">
                    </div>
                    <div class="search-field">
                        <label class="sr-only" for="ds-q2">Pesquisando</label>
                        <?php echo $icon('search', 'search-field__icon'); ?>
                        <input class="input" id="ds-q2" type="search" value="farmácia">
                        <span class="search-field__status"><span class="spinner" role="status"><span class="sr-only">Carregando</span></span></span>
                    </div>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-table">
                <h2 id="ds-table">Table</h2>
                <p>Cabeçalho ordenável com <code>aria-sort</code>. Abaixo de 768px vira cards (redimensione a janela).</p>
                <div class="table-wrap">
                    <table class="table table-responsive-cards">
                        <caption>Exemplo: diretório de ramais</caption>
                        <thead>
                            <tr>
                                <th scope="col" aria-sort="ascending"><button class="sort-btn" type="button">Setor <?php echo $icon('arrow-up-down'); ?></button></th>
                                <th scope="col" class="num"><button class="sort-btn" type="button">Ramal <?php echo $icon('arrow-up-down'); ?></button></th>
                                <th scope="col"><button class="sort-btn" type="button">Localização <?php echo $icon('arrow-up-down'); ?></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td data-label="Setor">Enfermagem</td><td data-label="Ramal" class="num">2080</td><td data-label="Localização">2º andar</td></tr>
                            <tr><td data-label="Setor">Financeiro</td><td data-label="Ramal" class="num">2100</td><td data-label="Localização">Prédio Administrativo</td></tr>
                            <tr><td data-label="Setor">Recepção</td><td data-label="Ramal" class="num">2010</td><td data-label="Localização">Térreo</td></tr>
                            <tr><td data-label="Setor">Tecnologia da Informação</td><td data-label="Ramal" class="num">2045</td><td data-label="Localização">1º andar</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="ds-section" aria-labelledby="ds-dd">
                <h2 id="ds-dd">Dropdown e Modal</h2>
                <div class="ds-row">
                    <div class="dropdown">
                        <button class="btn" type="button" data-dropdown-toggle aria-expanded="false" aria-controls="ds-menu">
                            Ações <?php echo $icon('chevron-down'); ?>
                        </button>
                        <ul class="dropdown__menu" id="ds-menu" hidden>
                            <li><a class="dropdown__item" href="#ds-dd"><?php echo $icon('download'); ?> Baixar PDF</a></li>
                            <li><a class="dropdown__item" href="#ds-dd"><?php echo $icon('external-link'); ?> Abrir em nova aba</a></li>
                            <li><a class="dropdown__item" href="#ds-dd" aria-disabled="true"><?php echo $icon('mail'); ?> Enviar (indisponível)</a></li>
                        </ul>
                    </div>
                    <button class="btn btn--primary" type="button" data-modal-open="ds-modal">Abrir modal</button>
                </div>
                <dialog class="modal" id="ds-modal" aria-labelledby="ds-modal-title">
                    <div class="modal__header">
                        <h3 class="modal__title" id="ds-modal-title">POP-ENF-042</h3>
                        <button class="icon-btn" type="button" data-modal-close><?php echo $icon('x'); ?><span class="sr-only">Fechar</span></button>
                    </div>
                    <div class="modal__body">
                        <p>Administração segura de medicamentos. Versão 4.2, revisado em março de 2026.</p>
                    </div>
                    <div class="modal__footer">
                        <button class="btn" type="button" data-modal-close>Fechar</button>
                        <button class="btn btn--primary" type="button"><?php echo $icon('download'); ?> Baixar</button>
                    </div>
                </dialog>
            </section>

            <section class="ds-section" aria-labelledby="ds-system">
                <h2 id="ds-system">SystemCard</h2>
                <p>Lista do acesso rápido (home) e card da página Sistemas. A cor do ícone é escolhida por sistema.</p>
                <div class="quick-panel">
                    <div class="section-head">
                        <div><p class="eyebrow">Acesso rápido</p><h3 class="section-head__title">Sistemas e ferramentas</h3></div>
                        <a class="link-arrow" href="#ds-system">Ver todos os sistemas <?php echo $icon('chevron-right'); ?></a>
                    </div>
                    <ul class="system-list">
                        <?php foreach ($systems as [$title, $desc, $ico, $tone]) : ?>
                            <li>
                                <a class="system-item" href="#ds-system">
                                    <span class="icon-tile tone-<?php echo $tone; ?>"><?php echo $icon($ico); ?></span>
                                    <span class="system-item__body">
                                        <span class="system-item__title"><?php echo $title; ?></span>
                                        <span class="system-item__desc"><?php echo $desc; ?></span>
                                    </span>
                                    <?php echo $icon('chevron-right', 'system-item__chevron'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <ul class="card-grid" style="margin-top: var(--space-6)">
                    <?php foreach (array_slice($systems, 0, 3) as [$title, $desc, $ico, $tone]) : ?>
                        <li class="system-card">
                            <span class="icon-tile tone-<?php echo $tone; ?>"><?php echo $icon($ico); ?></span>
                            <div>
                                <h3 class="system-card__title"><?php echo $title; ?></h3>
                                <p class="system-card__desc"><?php echo $desc; ?></p>
                            </div>
                            <p class="system-card__action"><a class="btn btn--sm" href="#ds-system">Acessar sistema <?php echo $icon('arrow-right'); ?></a></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="ds-section" aria-labelledby="ds-news">
                <h2 id="ds-news">NewsCard</h2>
                <ul class="card-grid">
                    <?php foreach ([
                        ['Institucional', '12 jun 2026', 'Hospital recebe certificação de qualidade nacional', 'Reconhecimento destaca a segurança do paciente e a gestão de processos.', 'teal', 'shield-check'],
                        ['Pessoas', '08 jun 2026', 'Semana da Enfermagem celebra quem transforma o cuidado', 'Programação inclui palestras, homenagens e atividades de bem-estar.', 'blue', 'heart-pulse'],
                        ['Saúde e bem-estar', '02 jun 2026', 'Nova campanha incentiva hábitos saudáveis no trabalho', 'Ações de alimentação, pausas ativas e acompanhamento nutricional.', 'green', 'activity'],
                    ] as [$cat, $date, $title, $excerpt, $tone, $ico]) : ?>
                        <li>
                            <article class="news-card is-clickable">
                                <div class="news-card__media ds-media tone-<?php echo $tone; ?>"><?php echo $icon($ico); ?></div>
                                <div class="news-card__body">
                                    <p class="news-card__meta"><span class="badge"><?php echo $cat; ?></span> <time><?php echo $date; ?></time></p>
                                    <h3 class="news-card__title"><a class="stretched-link" href="#ds-news"><?php echo $title; ?></a></h3>
                                    <p class="news-card__excerpt"><?php echo $excerpt; ?></p>
                                    <p class="news-card__more"><span class="link-arrow">Leia mais <?php echo $icon('arrow-right'); ?></span></p>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="ds-section" aria-labelledby="ds-docs">
                <h2 id="ds-docs">DocumentCard / ProtocolList</h2>
                <ul class="protocol-list">
                    <?php foreach ([
                        ['POP-ENF-042', 'Administração segura de medicamentos', 'Enfermagem', '4.2', 'success', 'Vigente'],
                        ['PRT-CCIH-018', 'Precauções e isolamento hospitalar', 'Controle de Infecção', '3.0', 'success', 'Vigente'],
                        ['POP-FAR-027', 'Armazenamento de medicamentos termolábeis', 'Farmácia', '2.1', 'warning', 'Em revisão'],
                        ['PRT-SEG-005', 'Identificação correta do paciente', 'Segurança do Paciente', '5.0', 'success', 'Vigente'],
                    ] as [$code, $title, $sector, $version, $status, $statusLabel]) : ?>
                        <li>
                            <a class="document-card" href="#ds-docs">
                                <span class="icon-tile tone-teal"><?php echo $icon('file-text'); ?></span>
                                <span class="document-card__body">
                                    <span class="document-card__code"><?php echo $code; ?></span>
                                    <span class="document-card__title"><?php echo $title; ?></span>
                                    <span class="document-card__meta">
                                        <span><?php echo $sector; ?></span>
                                        <span class="document-card__version">Versão <?php echo $version; ?></span>
                                        <span class="badge badge--<?php echo $status; ?>"><?php echo $statusLabel; ?></span>
                                    </span>
                                </span>
                                <?php echo $icon('chevron-right'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="ds-section" aria-labelledby="ds-events">
                <h2 id="ds-events">EventCard</h2>
                <ul class="card-grid">
                    <?php foreach ([
                        ['15', 'jun', 'Treinamento de Segurança do Paciente', '14:00', 'Auditório Principal'],
                        ['22', 'jun', 'Campanha de vacinação dos colaboradores', '08:00', 'Ambulatório'],
                        ['03', 'jul', 'Reunião geral de lideranças', '10:00', 'Sala de Reuniões 2'],
                    ] as [$day, $month, $title, $time, $place]) : ?>
                        <li>
                            <article class="event-card">
                                <p class="event-card__date" aria-hidden="true"><span class="event-card__day"><?php echo $day; ?></span><span class="event-card__month"><?php echo $month; ?></span></p>
                                <div>
                                    <h3 class="event-card__title"><a href="#ds-events"><span class="sr-only"><?php echo "$day de $month: "; ?></span><?php echo $title; ?></a></h3>
                                    <ul class="event-card__meta">
                                        <li><?php echo $icon('clock'); ?> <?php echo $time; ?></li>
                                        <li><?php echo $icon('map-pin'); ?> <?php echo $place; ?></li>
                                    </ul>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="ds-section" aria-labelledby="ds-loading">
                <h2 id="ds-loading">Carregando e vazio</h2>
                <div class="card-grid">
                    <div class="card" aria-hidden="true">
                        <span class="skeleton" style="height: 14px; width: 40%"></span>
                        <span class="skeleton" style="height: 20px; width: 90%; margin-top: 12px"></span>
                        <span class="skeleton" style="height: 14px; width: 70%; margin-top: 12px"></span>
                    </div>
                    <div class="card" style="display: grid; place-items: center"><span class="spinner" role="status"><span class="sr-only">Carregando</span></span></div>
                    <div class="empty-state"><?php echo $icon('search'); ?><p style="margin: 0">Nenhum resultado para “xyz”.</p></div>
                </div>
            </section>

        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
