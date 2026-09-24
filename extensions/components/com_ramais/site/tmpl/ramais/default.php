<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var \Hospital\Component\Ramais\Site\View\Ramais\HtmlView $this */

$search  = (string) $this->state->get('filter.search');
$sort    = (string) $this->state->get('list.sort');
$dir     = (string) $this->state->get('list.dir');
$action  = Route::_('index.php?option=com_ramais&view=ramais');
$columns = [
	'setor'       => Text::_('COM_RAMAIS_COL_SETOR'),
	'ramal'       => Text::_('COM_RAMAIS_COL_RAMAL'),
	'localizacao' => Text::_('COM_RAMAIS_COL_LOCALIZACAO'),
];

// Link de ordenação sem JavaScript: alterna asc/desc na mesma coluna
$sortLink = static function (string $column) use ($action, $search, $sort, $dir): string {
	$next  = ($sort === $column && $dir === 'ASC') ? 'desc' : 'asc';
	$query = http_build_query(array_filter(['q' => $search, 'sort' => $column, 'dir' => $next]));

	return $action . (str_contains($action, '?') ? '&' : '?') . $query;
};
$count = count($this->items);
?>
<div class="hi-directory hi-card" data-directory>
	<div class="hi-directory__toolbar">
		<form class="hi-search" role="search" method="get" action="<?php echo $this->escape($action); ?>" data-directory-form>
			<label for="ramais-q" class="visually-hidden"><?php echo Text::_('COM_RAMAIS_SEARCH_LABEL'); ?></label>
			<i class="fa-solid fa-magnifying-glass hi-search__icon" aria-hidden="true"></i>
			<input class="hi-search__input" type="search" id="ramais-q" name="q" value="<?php echo $this->escape($search); ?>"
				placeholder="<?php echo Text::_('COM_RAMAIS_SEARCH_PLACEHOLDER'); ?>" autocomplete="off" data-directory-search>
			<?php if ($sort !== 'setor' || $dir !== 'ASC') : ?>
				<input type="hidden" name="sort" value="<?php echo $this->escape($sort); ?>">
				<input type="hidden" name="dir" value="<?php echo strtolower($dir); ?>">
			<?php endif; ?>
		</form>
		<?php if ($intro = $this->params->get('intro')) : ?>
			<p class="hi-directory__intro"><?php echo $this->escape($intro); ?></p>
		<?php endif; ?>
	</div>

	<p class="hi-directory__count" aria-live="polite" data-directory-count
		data-one="<?php echo Text::_('COM_RAMAIS_COUNT_1'); ?>" data-many="<?php echo Text::_('COM_RAMAIS_COUNT_N'); ?>">
		<?php echo Text::plural('COM_RAMAIS_COUNT', $count); ?>
	</p>

	<table class="hi-table hi-table--stack" data-directory-table>
		<caption class="visually-hidden"><?php echo Text::_('COM_RAMAIS_TABLE_CAPTION_SITE'); ?></caption>
		<thead>
			<tr>
				<?php foreach ($columns as $key => $label) : ?>
					<?php $ariaSort = $sort === $key ? ($dir === 'ASC' ? 'ascending' : 'descending') : 'none'; ?>
					<th scope="col" class="hi-table__col--<?php echo $key; ?>" aria-sort="<?php echo $ariaSort; ?>">
						<a class="hi-table__sort" href="<?php echo $this->escape($sortLink($key)); ?>" data-sort="<?php echo $key; ?>" role="button">
							<?php echo $label; ?>
							<i class="fa-solid hi-table__sort-icon" aria-hidden="true"></i>
						</a>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $item) : ?>
				<tr>
					<th scope="row" data-label="<?php echo $columns['setor']; ?>" data-col="setor"><?php echo $this->escape($item->setor); ?></th>
					<td data-label="<?php echo $columns['ramal']; ?>" data-col="ramal" class="hi-table__num"><?php echo $this->escape($item->ramal); ?></td>
					<td data-label="<?php echo $columns['localizacao']; ?>" data-col="localizacao"><?php echo $this->escape($item->localizacao); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="hi-directory__empty"<?php echo $count ? ' hidden' : ''; ?> data-directory-empty>
		<i class="fa-solid fa-phone-slash" aria-hidden="true"></i>
		<?php echo Text::_('COM_RAMAIS_EMPTY'); ?>
	</p>
</div>
