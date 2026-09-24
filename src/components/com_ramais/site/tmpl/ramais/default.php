<?php

/**
 * Diretório de ramais (§13): busca, tabela com 3 colunas ordenáveis, cards no celular.
 *
 * Funciona sem JavaScript (busca e ordenação pelo servidor, via ?q= e ?sort=).
 * Com JavaScript (media/com_ramais/js/directory.js) filtra enquanto digita e
 * ordena sem recarregar a página.
 *
 * @var \HospitalSantaAurora\Component\Ramais\Site\View\Ramais\HtmlView $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use HospitalSantaAurora\Component\Ramais\Site\Model\RamaisModel;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$icon    = static fn (string $name, string $class = '') => LayoutHelper::render('hospital.icon', ['name' => $name, 'class' => $class]);
$search  = (string) $this->state->get('filter.search');
$sort    = (string) $this->state->get('list.ordering');
$dir     = $this->state->get('list.direction') === 'DESC' ? 'desc' : 'asc';
$action  = Route::_('index.php?option=com_ramais&view=ramais');
$visible = static fn (object $item): bool => $search === '' || RamaisModel::matches($item, $search);
$count   = \count(array_filter($this->items, $visible));
$columns = [
    'setor'       => ['COM_RAMAIS_COL_SETOR', ''],
    'ramal'       => ['COM_RAMAIS_COL_RAMAL', 'num'],
    'localizacao' => ['COM_RAMAIS_COL_LOCALIZACAO', ''],
];

$sortUrl = static function (string $key) use ($action, $search, $sort, $dir): string {
    $next  = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
    $query = ['sort' => $key, 'dir' => $next] + ($search !== '' ? ['q' => $search] : []);

    return $action . (str_contains($action, '?') ? '&' : '?') . http_build_query($query);
};
?>
<div class="directory" data-directory>
    <form class="directory__search" action="<?php echo $action; ?>" method="get" role="search" data-directory-form>
        <div class="search-field search-field--lg">
            <label class="sr-only" for="directory-q"><?php echo Text::_('COM_RAMAIS_SEARCH_LABEL'); ?></label>
            <?php echo $icon('search', 'search-field__icon'); ?>
            <input class="input" type="search" id="directory-q" name="q"
                   value="<?php echo $this->escape($search); ?>"
                   placeholder="<?php echo Text::_('COM_RAMAIS_SEARCH_PLACEHOLDER'); ?>"
                   autocomplete="off" spellcheck="false" data-directory-input>
            <button class="btn btn--primary" type="submit"><?php echo Text::_('COM_RAMAIS_SEARCH_SUBMIT'); ?></button>
        </div>
        <?php if ($sort !== 'setor' && \in_array($sort, array_keys($columns), true)) : ?>
            <input type="hidden" name="sort" value="<?php echo $this->escape($sort); ?>">
            <input type="hidden" name="dir" value="<?php echo $dir; ?>">
        <?php endif; ?>
    </form>

    <p class="directory__count" role="status" data-directory-count
       data-one="<?php echo Text::_('COM_RAMAIS_COUNT_1'); ?>"
       data-many="<?php echo Text::_('COM_RAMAIS_COUNT_MORE'); ?>"
       data-none="<?php echo Text::_('COM_RAMAIS_COUNT_0'); ?>">
        <?php echo Text::plural('COM_RAMAIS_COUNT', $count); ?>
    </p>

    <div class="table-wrap"<?php echo $count ? '' : ' hidden'; ?> data-directory-wrap>
        <table class="table table-responsive-cards directory__table" data-directory-table>
            <caption class="sr-only"><?php echo Text::_('COM_RAMAIS_TABLE_CAPTION_SITE'); ?></caption>
            <thead>
                <tr>
                    <?php foreach ($columns as $key => [$label, $class]) :
                        $ariaSort = $sort === $key ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none';
                        ?>
                        <th scope="col" class="<?php echo $class; ?>" aria-sort="<?php echo $ariaSort; ?>" data-sort-key="<?php echo $key; ?>">
                            <a class="sort-btn" href="<?php echo $this->escape($sortUrl($key)); ?>" data-sort-button>
                                <?php echo Text::_($label); ?>
                                <?php echo $icon('arrow-up-down', 'sort-btn__none'); ?>
                                <?php echo $icon('chevron-down', 'sort-btn__dir'); ?>
                                <span class="sr-only" data-sort-hint><?php echo Text::_('COM_RAMAIS_SORT_HINT'); ?></span>
                            </a>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr<?php echo $visible($item) ? '' : ' hidden'; ?>>
                        <td data-label="<?php echo Text::_('COM_RAMAIS_COL_SETOR'); ?>"><?php echo $this->escape($item->setor); ?></td>
                        <td data-label="<?php echo Text::_('COM_RAMAIS_COL_RAMAL'); ?>" class="num"><?php echo $this->escape($item->ramal); ?></td>
                        <td data-label="<?php echo Text::_('COM_RAMAIS_COL_LOCALIZACAO'); ?>"><?php echo $this->escape($item->localizacao); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="empty-state"<?php echo $count ? ' hidden' : ''; ?> data-directory-empty>
        <?php echo $icon('search'); ?>
        <p><?php echo Text::_('COM_RAMAIS_EMPTY'); ?></p>
        <?php if ($search !== '') : ?>
            <p><a class="link-arrow" href="<?php echo $action; ?>"><?php echo Text::_('COM_RAMAIS_CLEAR_SEARCH'); ?></a></p>
        <?php endif; ?>
    </div>
</div>
