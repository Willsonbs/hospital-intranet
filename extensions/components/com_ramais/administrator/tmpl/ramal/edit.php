<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Ramais\Administrator\View\Ramal\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_ramais&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="main-card p-4">
		<div class="row">
			<div class="col-lg-7">
				<?php echo $this->form->renderFieldset('details'); ?>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
