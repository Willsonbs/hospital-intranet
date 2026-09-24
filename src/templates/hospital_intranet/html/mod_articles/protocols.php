<?php

/**
 * Últimos protocolos adicionados (mod_articles, categorias da Biblioteca).
 * Campos: doc-codigo, doc-versao, doc-setor, doc-status.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if (!$list) {
    return;
}

$catids = array_filter((array) $params->get('catid', []));

if ($catids) {
    $module->sectionLink = [
        'url'  => Route::_(RouteHelper::getCategoryRoute((int) reset($catids))),
        'text' => Text::_('TPL_HOSPITAL_INTRANET_ALL_PROTOCOLS'),
    ];
}

$statusBadge = [
    'vigente'    => ['badge--success', 'circle-check'],
    'em-revisao' => ['badge--warning', 'clock'],
    'obsoleto'   => ['badge--danger', 'circle-x'],
];
?>
<ul class="protocol-list">
    <?php foreach ($list as $item) :
        $code    = (string) T::raw($item, 'doc-codigo');
        $version = (string) T::raw($item, 'doc-versao');
        $sector  = T::text($item, 'doc-setor');
        $status  = (string) T::raw($item, 'doc-status', 'vigente');
        [$badgeClass, $badgeIcon] = $statusBadge[$status] ?? $statusBadge['vigente'];
        ?>
        <li>
            <a class="document-card" href="<?php echo T::e($item->link); ?>">
                <span class="icon-tile tone-teal"><?php echo T::icon('file-text'); ?></span>
                <span class="document-card__body">
                    <?php if ($code !== '') : ?>
                        <span class="document-card__code"><?php echo T::e($code); ?></span>
                    <?php endif; ?>
                    <span class="document-card__title"><?php echo T::e($item->title); ?></span>
                    <span class="document-card__meta">
                        <?php if ($sector !== '') : ?>
                            <span><?php echo T::e($sector); ?></span>
                        <?php endif; ?>
                        <?php if ($version !== '') : ?>
                            <span class="document-card__version"><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_VERSION', T::e($version)); ?></span>
                        <?php endif; ?>
                        <?php if ($status !== 'vigente') : ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo T::icon($badgeIcon); ?> <?php echo T::e(T::text($item, 'doc-status')); ?></span>
                        <?php endif; ?>
                    </span>
                </span>
                <?php echo T::icon('chevron-right'); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
