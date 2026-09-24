<?php

/**
 * Ficha do documento (§11): download em destaque, status, dados de controle.
 *
 * @var \Joomla\Component\Content\Site\View\Article\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$item    = $this->item;
$status  = (string) T::raw($item, 'doc-status', 'vigente');
$file    = T::file($item, 'doc-arquivo');
$publico = (array) T::raw($item, 'doc-publico', []);
$badges  = ['vigente' => ['badge--success', 'circle-check'], 'em-revisao' => ['badge--warning', 'clock'], 'obsoleto' => ['badge--danger', 'circle-x']];
[$badgeClass, $badgeIcon] = $badges[$status] ?? $badges['vigente'];

$details = array_filter([
    'TPL_HOSPITAL_INTRANET_DOC_CODE'     => T::e((string) T::raw($item, 'doc-codigo')),
    'TPL_HOSPITAL_INTRANET_DOC_VERSION'  => T::e((string) T::raw($item, 'doc-versao')),
    'TPL_HOSPITAL_INTRANET_DOC_TYPE'     => T::e($item->category_title),
    'TPL_HOSPITAL_INTRANET_DOCS_SECTOR'  => T::e(T::text($item, 'doc-setor')),
    'TPL_HOSPITAL_INTRANET_DOC_PUBLISHED'=> HTMLHelper::_('date', $item->publish_up, 'd/m/Y'),
    'TPL_HOSPITAL_INTRANET_DOC_REVIEW'   => T::date($item, 'doc-revisao'),
    'TPL_HOSPITAL_INTRANET_DOC_AUDIENCE' => T::e(T::text($item, 'doc-publico')),
    'TPL_HOSPITAL_INTRANET_DOC_AUTHOR'   => T::e($item->created_by_alias ?: $item->author),
], static fn ($v) => $v !== '');
?>
<div class="doc-page">
    <div class="doc-page__main">
        <p class="doc-page__badges">
            <span class="badge"><?php echo T::e($item->category_title); ?></span>
            <span class="badge <?php echo $badgeClass; ?>"><?php echo T::icon($badgeIcon); ?> <?php echo T::e(T::text($item, 'doc-status') ?: Text::_('TPL_HOSPITAL_INTRANET_STATUS_VIGENTE')); ?></span>
        </p>

        <?php if ($status === 'obsoleto') : ?>
            <div class="alert alert--danger" role="note">
                <?php echo T::icon('circle-x'); ?>
                <div class="alert__body">
                    <span class="alert__label"><?php echo Text::_('TPL_HOSPITAL_INTRANET_STATUS_OBSOLETO'); ?></span>
                    <p class="alert__text"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_OBSOLETE_TEXT'); ?></p>
                </div>
            </div>
        <?php elseif ($status === 'em-revisao') : ?>
            <div class="alert alert--warning" role="note">
                <?php echo T::icon('clock'); ?>
                <div class="alert__body">
                    <span class="alert__label"><?php echo Text::_('TPL_HOSPITAL_INTRANET_STATUS_EM_REVISAO'); ?></span>
                    <p class="alert__text"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_REVIEW_TEXT'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="prose"><?php echo $item->text; ?></div>

        <p class="back-link">
            <a class="link-arrow" href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->catid, $item->language)); ?>">
                <?php echo T::icon('chevron-left'); ?> <?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_BACK_TO', T::e($item->category_title)); ?>
            </a>
        </p>
    </div>

    <aside class="doc-page__aside card" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_INFO'); ?>">
        <?php if ($file) : ?>
            <a class="btn btn--primary btn--block" href="<?php echo T::e($file['url']); ?>" download>
                <?php echo T::icon('download'); ?> <?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_DOWNLOAD'); ?>
            </a>
            <p class="doc-page__file">
                <?php echo T::e(trim($file['ext'] . ($file['size'] ? ' · ' . $file['size'] : ''))); ?>
                <?php if ($file['ext'] === 'PDF') : ?>
                    · <a href="<?php echo T::e($file['url']); ?>" target="_blank" rel="noopener"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_OPEN'); ?><span class="sr-only"> <?php echo Text::_('TPL_HOSPITAL_INTRANET_NEW_WINDOW'); ?></span></a>
                <?php endif; ?>
            </p>
        <?php else : ?>
            <p class="doc-page__nofile"><?php echo T::icon('info'); ?> <?php echo Text::_('TPL_HOSPITAL_INTRANET_DOC_NO_FILE'); ?></p>
        <?php endif; ?>

        <dl class="meta-list">
            <?php foreach ($details as $label => $value) : ?>
                <div>
                    <dt><?php echo Text::_($label); ?></dt>
                    <dd><?php echo $value; ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </aside>
</div>
