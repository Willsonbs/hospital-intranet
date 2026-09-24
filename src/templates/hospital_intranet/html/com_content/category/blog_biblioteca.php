<?php

/**
 * Biblioteca institucional (§10, §11): documentos com busca (código, título, descrição)
 * e filtros por tipo, setor e status. Sem paginação: a lista inteira vai para a página
 * e o filtro em tempo real (list-filter.js) roda no navegador; sem JS, o servidor filtra
 * pelos mesmos parâmetros (?q=&tipo=&setor=&status=).
 *
 * Campos: doc-codigo, doc-versao, doc-status, doc-setor, doc-revisao, doc-arquivo.
 *
 * @var \Joomla\Component\Content\Site\View\Category\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$this->getDocument()->getWebAssetManager()->useScript('template.hospital_intranet.listfilter');

$input   = Factory::getApplication()->getInput();
$filters = [
    'q'      => trim($input->getString('q', '')),
    'tipo'   => $input->getCmd('tipo', ''),
    'setor'  => $input->getCmd('setor', ''),
    'status' => $input->getCmd('status', ''),
];

$statusBadge = [
    'vigente'    => ['badge--success', 'circle-check'],
    'em-revisao' => ['badge--warning', 'clock'],
    'obsoleto'   => ['badge--danger', 'circle-x'],
];

// Tipo = subcategoria de 1º nível abaixo da Biblioteca (Protocolos, POPs, Manuais…)
$types = [];

foreach (T::filterCategories($this->category, $this->params) as $node) {
    $types[(int) $node->id] = $node->title;
}

$typeOf = static function (int $catid) use ($types): int {
    for ($node = T::category($catid); $node; $node = $node->getParent()) {
        if (isset($types[(int) $node->id])) {
            return (int) $node->id;
        }
    }

    return 0;
};

// Monta as linhas uma vez: dados, texto de busca e se passam nos filtros atuais
$rows    = [];
$sectors = [];

foreach (T::categoryItems(array_merge($this->lead_items, $this->intro_items, $this->link_items), $this->params) as $item) {
    $row = [
        'item'    => $item,
        'code'    => (string) T::raw($item, 'doc-codigo'),
        'version' => (string) T::raw($item, 'doc-versao'),
        'status'  => (string) T::raw($item, 'doc-status', 'vigente'),
        'setor'   => (string) T::raw($item, 'doc-setor'),
        'sector'  => T::text($item, 'doc-setor'),
        'review'  => T::date($item, 'doc-revisao'),
        'file'    => T::file($item, 'doc-arquivo'),
        'tipo'    => (string) $typeOf((int) $item->catid),
    ];
    $row['q'] = T::normalize(implode(' ', [$row['code'], $item->title, strip_tags($item->introtext), $row['sector']]));

    $row['match'] = ($filters['q'] === '' || T::matches($row['q'], $filters['q']))
        && ($filters['tipo'] === '' || $filters['tipo'] === $row['tipo'])
        && ($filters['setor'] === '' || $filters['setor'] === $row['setor'])
        && ($filters['status'] === '' || $filters['status'] === $row['status']);

    if ($row['setor'] !== '') {
        $sectors[$row['setor']] = $row['sector'];
    }

    $rows[] = $row;
}

asort($sectors, SORT_LOCALE_STRING);
$visible = \count(array_filter($rows, static fn ($r) => $r['match']));
?>
<div class="page-section" data-filter-root>
    <?php if ($this->category->description) : ?>
        <div class="page-intro"><?php echo $this->category->description; ?></div>
    <?php endif; ?>

    <form class="filter-bar" method="get" role="search" data-filter-form>
        <div class="search-field filter-bar__search">
            <label class="sr-only" for="docs-q"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_SEARCH'); ?></label>
            <?php echo T::icon('search', 'search-field__icon'); ?>
            <input class="input" type="search" id="docs-q" name="q" value="<?php echo T::e($filters['q']); ?>"
                   placeholder="<?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_SEARCH'); ?>..." autocomplete="off" data-filter="q">
        </div>

        <?php if (\count($types) > 1) : ?>
            <div class="field filter-bar__field">
                <label class="field__label" for="docs-tipo"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_TYPE'); ?></label>
                <select class="select" id="docs-tipo" name="tipo" data-filter="tipo">
                    <option value=""><?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL_M'); ?></option>
                    <?php foreach ($types as $id => $title) : ?>
                        <option value="<?php echo $id; ?>"<?php echo $filters['tipo'] === (string) $id ? ' selected' : ''; ?>><?php echo T::e($title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="field filter-bar__field">
            <label class="field__label" for="docs-setor"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_SECTOR'); ?></label>
            <select class="select" id="docs-setor" name="setor" data-filter="setor">
                <option value=""><?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL_M'); ?></option>
                <?php foreach ($sectors as $value => $label) : ?>
                    <option value="<?php echo T::e($value); ?>"<?php echo $filters['setor'] === $value ? ' selected' : ''; ?>><?php echo T::e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field filter-bar__field">
            <label class="field__label" for="docs-status"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_STATUS'); ?></label>
            <select class="select" id="docs-status" name="status" data-filter="status">
                <option value=""><?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL_M'); ?></option>
                <?php foreach (['vigente', 'em-revisao', 'obsoleto'] as $status) : ?>
                    <option value="<?php echo $status; ?>"<?php echo $filters['status'] === $status ? ' selected' : ''; ?>>
                        <?php echo Text::_('TPL_HOSPITAL_INTRANET_STATUS_' . strtoupper(str_replace('-', '_', $status))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn--primary filter-bar__submit" type="submit"><?php echo Text::_('TPL_HOSPITAL_INTRANET_FILTER'); ?></button>
    </form>

    <p class="list-count" role="status" data-filter-count
       data-one="<?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_COUNT_1'); ?>"
       data-many="<?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_COUNT_MORE'); ?>"
       data-none="<?php echo Text::_('TPL_HOSPITAL_INTRANET_DOCS_COUNT_0'); ?>">
        <?php echo Text::plural('TPL_HOSPITAL_INTRANET_DOCS_COUNT', $visible); ?>
    </p>

    <?php if ($rows) : ?>
        <ul class="doc-list">
            <?php foreach ($rows as $row) :
                $item = $row['item'];
                [$badgeClass, $badgeIcon] = $statusBadge[$row['status']] ?? $statusBadge['vigente'];
                ?>
                <li class="doc-row" data-filter-item data-q="<?php echo T::e($row['q']); ?>" data-tipo="<?php echo $row['tipo']; ?>"
                    data-setor="<?php echo T::e($row['setor']); ?>" data-status="<?php echo T::e($row['status']); ?>"<?php echo $row['match'] ? '' : ' hidden'; ?>>
                    <span class="icon-tile tone-teal"><?php echo T::icon('file-text'); ?></span>
                    <div class="doc-row__body">
                        <?php if ($row['code'] !== '') : ?>
                            <span class="document-card__code"><?php echo T::e($row['code']); ?></span>
                        <?php endif; ?>
                        <h2 class="doc-row__title"><a href="<?php echo T::e($item->link); ?>"><?php echo T::e($item->title); ?></a></h2>
                        <p class="document-card__meta">
                            <span><?php echo T::e($types[(int) $row['tipo']] ?? $item->category_title); ?></span>
                            <?php if ($row['sector'] !== '') : ?><span><?php echo T::e($row['sector']); ?></span><?php endif; ?>
                            <?php if ($row['version'] !== '') : ?><span class="document-card__version"><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_VERSION', T::e($row['version'])); ?></span><?php endif; ?>
                            <?php if ($row['review'] !== '') : ?><span><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_DOCS_REVIEW', $row['review']); ?></span><?php endif; ?>
                            <?php if ($row['status'] !== 'vigente') : ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo T::icon($badgeIcon); ?> <?php echo T::e(T::text($item, 'doc-status')); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if ($row['file']) : ?>
                        <a class="btn btn--sm doc-row__download" href="<?php echo T::e($row['file']['url']); ?>" download>
                            <?php echo T::icon('download'); ?>
                            <span><?php echo T::e($row['file']['ext']); ?></span>
                            <span class="sr-only"><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_DOCS_DOWNLOAD_SR', T::e($item->title), $row['file']['size']); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="empty-state"<?php echo $visible ? ' hidden' : ''; ?> data-filter-empty>
        <?php echo T::icon('search'); ?>
        <p><?php echo Text::_($rows ? 'TPL_HOSPITAL_INTRANET_DOCS_NONE_FOUND' : 'TPL_HOSPITAL_INTRANET_DOCS_EMPTY'); ?></p>
    </div>
</div>
