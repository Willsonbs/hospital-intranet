<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Ramais\Administrator\View\Ramais\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('table.columns')->useScript('multiselect');

$user      = Factory::getApplication()->getIdentity();
$canEdit   = $user->authorise('core.edit', 'com_ramais');
$canState  = $user->authorise('core.edit.state', 'com_ramais');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_ramais&view=ramais'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="j-main-container">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table" id="ramaisList">
				<caption class="visually-hidden"><?php echo Text::_('COM_RAMAIS_TABLE_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
						<th scope="col" class="w-1 text-center">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_RAMAIS_FIELD_STATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th scope="col">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_RAMAIS_FIELD_SETOR', 'a.setor', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-10">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_RAMAIS_FIELD_RAMAL', 'a.ramal', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-25 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_RAMAIS_FIELD_LOCALIZACAO', 'a.localizacao', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-5 d-none d-md-table-cell text-center">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_RAMAIS_FIELD_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-5 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->setor); ?></td>
							<td class="text-center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'ramais.', $canState); ?>
							</td>
							<th scope="row">
								<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_ramais&task=ramal.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->setor); ?>
									</a>
								<?php else : ?>
									<?php echo $this->escape($item->setor); ?>
								<?php endif; ?>
							</th>
							<td><?php echo $this->escape($item->ramal); ?></td>
							<td class="d-none d-md-table-cell"><?php echo $this->escape($item->localizacao); ?></td>
							<td class="d-none d-md-table-cell text-center"><?php echo (int) $item->ordering; ?></td>
							<td class="d-none d-md-table-cell"><?php echo (int) $item->id; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
