<?php

/**
 * Aviso completo: prioridade, texto e validade.
 *
 * @var \Joomla\Component\Content\Site\View\Article\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$item  = $this->item;
$style = T::alertStyle($item);
?>
<div class="alert <?php echo $style['class']; ?> alert--page" role="note">
    <?php echo T::icon($style['icon']); ?>
    <div class="alert__body">
        <span class="alert__label"><?php echo Text::_($style['label']); ?></span>
        <div class="prose"><?php echo $item->text; ?></div>
        <?php if ($item->publish_down) : ?>
            <p class="alert__text"><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_ALERT_UNTIL', HTMLHelper::_('date', $item->publish_down, 'd/m/Y H:i')); ?></p>
        <?php endif; ?>
    </div>
</div>
