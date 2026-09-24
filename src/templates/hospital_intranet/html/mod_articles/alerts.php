<?php

/**
 * Avisos importantes (mod_articles, categoria Avisos), exibidos no hero da home.
 * O período de exibição é o Início/Fim da publicação do artigo.
 * Ordem: crítico, atenção, informativo (campo avi-prioridade).
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\Layout\LayoutHelper;

if (!$list) {
    return;
}
?>
<div class="alert-stack">
    <?php foreach (T::sortAlerts($list) as $item) : ?>
        <?php echo LayoutHelper::render('hospital.alert', ['item' => $item]); ?>
    <?php endforeach; ?>
</div>
