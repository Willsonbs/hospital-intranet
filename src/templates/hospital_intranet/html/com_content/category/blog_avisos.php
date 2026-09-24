<?php

/**
 * Avisos vigentes (página oculta /avisos; o link "Saiba mais" leva a cada aviso).
 *
 * @var \Joomla\Component\Content\Site\View\Category\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$items = T::sortAlerts(T::categoryItems(array_merge($this->lead_items, $this->intro_items, $this->link_items), $this->params));
?>
<div class="page-section">
    <?php if ($items) : ?>
        <div class="alert-stack">
            <?php foreach ($items as $item) : ?>
                <?php echo LayoutHelper::render('hospital.alert', ['item' => $item]); ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-state">
            <?php echo T::icon('circle-check'); ?>
            <p><?php echo Text::_('TPL_HOSPITAL_INTRANET_ALERTS_EMPTY'); ?></p>
        </div>
    <?php endif; ?>
</div>
