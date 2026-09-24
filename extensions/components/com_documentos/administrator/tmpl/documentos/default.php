<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Documentos\Administrator\View\Documentos\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('table.columns')->useScript('multiselect');

$user      = $this->getCurrentUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$badge     = ['vigente' => 'bg-success', 'em_revisao' => 'bg-warning text-dark', 'obsoleto' => 'bg-secondary'];
?>
<form action="<?php echo Route::_('index.php?option=com_documentos&view=documentos'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="j-main-container">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table" id="documentosList">
				<caption class="visually-hidden"><?php echo Text::_('COM_DOCUMENTOS_TABLE_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
						<th scope="col" class="w-1 text-center"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?></th>
						<th scope="col" class="w-10"><?php echo HTMLHelper::_('searchtools.sort', 'COM_DOCUMENTOS_FIELD_CODIGO', 'a.codigo', $listDirn, $listOrder); ?></th>
						<th scope="col"><?php echo HTMLHelper::_('searchtools.sort', 'COM_DOCUMENTOS_FIELD_TITULO', 'a.titulo', $listDirn, $listOrder); ?></th>
						<th scope="col" class="w-10 d-none d-md-table-cell"><?php echo HTMLHelper::_('searchtools.sort', 'COM_DOCUMENTOS_FIELD_SETOR', 'a.setor', $listDirn, $listOrder); ?></th>
						<th scope="col" class="w-5 d-none d-md-table-cell"><?php echo Text::_('COM_DOCUMENTOS_FIELD_VERSAO'); ?></th>
						<th scope="col" class="w-10"><?php echo HTMLHelper::_('searchtools.sort', 'COM_DOCUMENTOS_FIELD_STATUS', 'a.status', $listDirn, $listOrder); ?></th>
						<th scope="col" class="w-10 d-none d-lg-table-cell"><?php echo HTMLHelper::_('searchtools.sort', 'COM_DOCUMENTOS_FIELD_DATA_REVISAO', 'a.data_revisao', $listDirn, $listOrder); ?></th>
						<th scope="col" class="w-5 d-none d-lg-table-cell"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<?php
						$canEdit  = $user->authorise('core.edit', 'com_documentos.category.' . (int) $item->catid);
						$canState = $user->authorise('core.edit.state', 'com_documentos.category.' . (int) $item->catid);
						$overdue  = $item->status !== 'obsoleto' && DocumentosHelper::isRevisionOverdue($item->data_revisao);
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->titulo); ?></td>
							<td class="text-center"><?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'documentos.', $canState); ?></td>
							<td><code><?php echo $this->escape($item->codigo); ?></code></td>
							<th scope="row">
								<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_documentos&task=documento.edit&id=' . (int) $item->id); ?>"><?php echo $this->escape($item->titulo); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->titulo); ?>
								<?php endif; ?>
								<div class="small text-muted"><?php echo Text::_('JCATEGORY'); ?>: <?php echo $this->escape($item->category_title); ?></div>
							</th>
							<td class="d-none d-md-table-cell"><?php echo $this->escape($item->setor); ?></td>
							<td class="d-none d-md-table-cell"><?php echo $this->escape($item->versao); ?></td>
							<td><span class="badge <?php echo $badge[$item->status] ?? 'bg-secondary'; ?>"><?php echo DocumentosHelper::statusLabel($item->status); ?></span></td>
							<td class="d-none d-lg-table-cell">
								<?php echo DocumentosHelper::formatDate($item->data_revisao); ?>
								<?php if ($overdue) : ?>
									<span class="badge bg-danger"><?php echo Text::_('COM_DOCUMENTOS_REVISION_OVERDUE'); ?></span>
								<?php endif; ?>
							</td>
							<td class="d-none d-lg-table-cell"><?php echo (int) $item->id; ?></td>
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
