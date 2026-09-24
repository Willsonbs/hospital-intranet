<?php

/**
 * Aviso (home e página de avisos). $displayData: item, link (bool, mostrar "Saiba mais").
 * Prioridade pelo campo avi-prioridade: critico, atencao (padrão), informativo.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;

$item  = $displayData['item'];
$style = T::alertStyle($item);
$more  = ($displayData['link'] ?? true) && trim(strip_tags((string) ($item->fulltext ?? ''))) !== '';
?>
<div class="alert <?php echo $style['class']; ?>" role="note">
    <?php echo T::icon($style['icon']); ?>
    <div class="alert__body">
        <span class="alert__label"><?php echo Text::_($style['label']); ?></span>
        <p class="alert__title"><?php echo T::e($item->title); ?></p>
        <?php if (trim(strip_tags((string) $item->introtext)) !== '') : ?>
            <p class="alert__text"><?php echo T::e(T::excerpt($item->introtext, 180)); ?></p>
        <?php endif; ?>
        <?php if ($more) : ?>
            <a class="link-arrow" href="<?php echo T::e($item->link); ?>">
                <?php echo Text::_('TPL_HOSPITAL_INTRANET_LEARN_MORE'); ?><span class="sr-only">: <?php echo T::e($item->title); ?></span>
                <?php echo T::icon('arrow-right'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
