<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 *
 * @var \HospitalSantaAurora\Component\Ramais\Administrator\View\Ramal\HtmlView $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$this->getDocument()->getWebAssetManager()
    ->useScript('keepalive')
    ->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_ramais&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="ramal-form" class="form-validate"
      aria-label="<?php echo Text::_((int) $this->item->id === 0 ? 'COM_RAMAIS_RAMAL_NEW' : 'COM_RAMAIS_RAMAL_EDIT', true); ?>">
    <div class="main-card p-4">
        <div class="row">
            <div class="col-lg-8">
                <?php echo $this->form->renderFieldset('details'); ?>
            </div>
            <div class="col-lg-4">
                <?php echo $this->form->renderFieldset('status'); ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo $this->form->renderField('id'); ?>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
