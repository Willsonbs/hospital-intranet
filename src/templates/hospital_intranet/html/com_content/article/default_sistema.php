<?php

/**
 * Página de um sistema: descrição e botão de acesso (normalmente se chega direto ao
 * sistema pelos cards; esta página cobre links antigos e resultados de busca).
 *
 * @var \Joomla\Component\Content\Site\View\Article\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;

$item   = $this->item;
$url    = (string) T::raw($item, 'sis-url');
$newTab = T::raw($item, 'sis-nova-aba', '1') === '1';
?>
<div class="card system-page">
    <span class="icon-tile <?php echo T::tone((string) T::raw($item, 'sis-cor', 'teal')); ?>"><?php echo T::icon((string) T::raw($item, 'sis-icone', 'layout-grid')); ?></span>
    <div class="prose"><?php echo $item->text; ?></div>
    <?php if ($url !== '') : ?>
        <p>
            <a class="btn btn--primary" href="<?php echo T::e($url); ?>"<?php echo $newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo Text::_('TPL_HOSPITAL_INTRANET_SYSTEM_OPEN'); ?>
                <?php if ($newTab) : ?><span class="sr-only"> <?php echo Text::_('TPL_HOSPITAL_INTRANET_NEW_WINDOW'); ?></span><?php endif; ?>
                <?php echo T::icon($newTab ? 'external-link' : 'arrow-right'); ?>
            </a>
        </p>
    <?php endif; ?>
</div>
