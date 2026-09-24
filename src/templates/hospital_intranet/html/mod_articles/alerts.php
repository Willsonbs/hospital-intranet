<?php

/**
 * Avisos importantes (mod_articles, categoria Avisos), exibidos no hero da home.
 * O período de exibição é o Início/Fim da publicação do artigo.
 * Campo: avi-prioridade (critico, atencao, informativo) — ordena e define o estilo.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;

if (!$list) {
    return;
}

$styles = [
    'critico'     => ['alert--danger', 'circle-x', 'TPL_HOSPITAL_INTRANET_ALERT_CRITICAL'],
    'atencao'     => ['alert--warning', 'triangle-alert', 'TPL_HOSPITAL_INTRANET_ALERT_IMPORTANT'],
    'informativo' => ['', 'info', 'TPL_HOSPITAL_INTRANET_ALERT_INFO'],
];
$rank = array_flip(array_keys($styles));

// Mais urgentes primeiro; mesma prioridade mantém a ordem do módulo (mais recentes)
$priority = static fn ($item) => $rank[T::raw($item, 'avi-prioridade', 'atencao')] ?? $rank['atencao'];
$items    = array_values($list);
usort($items, static fn ($a, $b) => $priority($a) <=> $priority($b));
?>
<div class="alert-stack">
    <?php foreach ($items as $item) :
        [$class, $icon, $label] = $styles[T::raw($item, 'avi-prioridade', 'atencao')] ?? $styles['atencao'];
        $hasMore = trim(strip_tags((string) ($item->fulltext ?? ''))) !== '';
        ?>
        <div class="alert <?php echo $class; ?>" role="note">
            <?php echo T::icon($icon); ?>
            <div class="alert__body">
                <span class="alert__label"><?php echo Text::_($label); ?></span>
                <p class="alert__title"><?php echo T::e($item->title); ?></p>
                <?php if (trim(strip_tags($item->introtext)) !== '') : ?>
                    <p class="alert__text"><?php echo T::e(T::excerpt($item->introtext, 180)); ?></p>
                <?php endif; ?>
                <?php if ($hasMore) : ?>
                    <a class="link-arrow" href="<?php echo T::e($item->link); ?>">
                        <?php echo Text::_('TPL_HOSPITAL_INTRANET_LEARN_MORE'); ?><span class="sr-only">: <?php echo T::e($item->title); ?></span>
                        <?php echo T::icon('arrow-right'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
