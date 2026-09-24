<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Documentos\Administrator\View\Documento\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_documentos&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="row">
		<div class="col-lg-8">
			<div class="main-card p-4">
				<h2 class="h5 mb-3"><?php echo Text::_('COM_DOCUMENTOS_FIELDSET_DETAILS'); ?></h2>
				<?php echo $this->form->renderFieldset('details'); ?>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="main-card p-4">
				<h2 class="h5 mb-3"><?php echo Text::_('COM_DOCUMENTOS_FIELDSET_CONTROL'); ?></h2>
				<?php echo $this->form->renderFieldset('control'); ?>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
