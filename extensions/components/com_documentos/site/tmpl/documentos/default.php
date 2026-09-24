<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Documentos\Site\View\Documentos\HtmlView $this */

$action   = Route::_('index.php?option=com_documentos&view=documentos');
$search   = (string) $this->state->get('filter.search');
$cat      = (int) $this->state->get('filter.cat');
$setor    = (string) $this->state->get('filter.setor');
$status   = (string) $this->state->get('filter.status');
$filtered = $search !== '' || $cat || $setor !== '' || $status !== '';
$total    = $this->pagination->total;
$layouts  = JPATH_SITE . '/components/com_documentos/layouts';
?>
<div class="hi-library">
	<form class="hi-card hi-filterbar" method="get" action="<?php echo $this->escape($action); ?>" role="search">
		<div class="hi-search">
			<label for="doc-q" class="visually-hidden"><?php echo Text::_('COM_DOCUMENTOS_SEARCH_LABEL'); ?></label>
			<i class="fa-solid fa-magnifying-glass hi-search__icon" aria-hidden="true"></i>
			<input class="hi-search__input" type="search" id="doc-q" name="q" value="<?php echo $this->escape($search); ?>"
				placeholder="<?php echo Text::_('COM_DOCUMENTOS_SEARCH_PLACEHOLDER'); ?>" autocomplete="off">
		</div>

		<?php if (count($this->categories) > 1) : ?>
			<div class="hi-field">
				<label for="doc-cat"><?php echo Text::_('COM_DOCUMENTOS_FILTER_CATEGORY'); ?></label>
				<select class="hi-select" id="doc-cat" name="cat">
					<option value=""><?php echo Text::_('COM_DOCUMENTOS_ALL_FEM'); ?></option>
					<?php foreach ($this->categories as $category) : ?>
						<option value="<?php echo (int) $category->id; ?>"<?php echo $cat === (int) $category->id ? ' selected' : ''; ?>><?php echo $this->escape($category->title); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<div class="hi-field">
			<label for="doc-setor"><?php echo Text::_('COM_DOCUMENTOS_FILTER_SETOR_LABEL'); ?></label>
			<select class="hi-select" id="doc-setor" name="setor">
				<option value=""><?php echo Text::_('COM_DOCUMENTOS_ALL_MASC'); ?></option>
				<?php foreach ($this->setores as $option) : ?>
					<option value="<?php echo $this->escape($option); ?>"<?php echo $setor === $option ? ' selected' : ''; ?>><?php echo $this->escape($option); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="hi-field">
			<label for="doc-status"><?php echo Text::_('COM_DOCUMENTOS_FILTER_STATUS_LABEL'); ?></label>
			<select class="hi-select" id="doc-status" name="status">
				<option value=""><?php echo Text::_('COM_DOCUMENTOS_STATUS_CURRENT_AND_REVIEW'); ?></option>
				<?php foreach (['vigente', 'em_revisao', 'obsoleto'] as $option) : ?>
					<option value="<?php echo $option; ?>"<?php echo $status === $option ? ' selected' : ''; ?>><?php echo Text::_('COM_DOCUMENTOS_STATUS_' . strtoupper($option)); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="hi-filterbar__actions">
			<button type="submit" class="hi-btn"><?php echo Text::_('COM_DOCUMENTOS_FILTER_SUBMIT'); ?></button>
			<?php if ($filtered) : ?>
				<a class="hi-btn hi-btn--ghost" href="<?php echo $this->escape($action); ?>"><?php echo Text::_('COM_DOCUMENTOS_FILTER_CLEAR'); ?></a>
			<?php endif; ?>
		</div>
	</form>

	<?php if ($intro = $this->params->get('intro')) : ?>
		<p class="hi-library__intro"><?php echo $this->escape($intro); ?></p>
	<?php endif; ?>

	<p class="hi-library__count" aria-live="polite"><?php echo Text::plural('COM_DOCUMENTOS_N_RESULTS', $total); ?></p>

	<?php if ($this->items) : ?>
		<ul class="hi-doc-list">
			<?php foreach ($this->items as $item) : ?>
				<li>
					<?php echo LayoutHelper::render('documento-row', [
						'item' => $item,
						'link' => Route::_('index.php?option=com_documentos&view=documento&id=' . (int) $item->id),
					], $layouts); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="hi-empty">
			<i class="fa-regular fa-folder-open" aria-hidden="true"></i>
			<?php echo Text::_('COM_DOCUMENTOS_EMPTY'); ?>
		</p>
	<?php endif; ?>

	<?php if ($this->pagination->pagesTotal > 1) : ?>
		<div class="hi-pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	<?php endif; ?>
</div>
