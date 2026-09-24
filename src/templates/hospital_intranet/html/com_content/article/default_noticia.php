<?php

/**
 * Notícia: categoria, data, imagem e texto.
 *
 * @var \Joomla\Component\Content\Site\View\Article\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$item   = $this->item;
$images = json_decode($item->images ?? '{}');
$src    = $images->image_fulltext ?? '' ?: ($images->image_intro ?? '');
$alt    = !empty($images->image_fulltext) ? ($images->image_fulltext_alt ?? '') : ($images->image_intro_alt ?? '');
$image  = $src ? HTMLHelper::_('cleanImageURL', $src) : null;

$root = T::category((int) $item->catid);

while ($root && (int) $root->level > 1) {
    $root = $root->getParent();
}
?>
<article class="news-article">
    <p class="news-card__meta">
        <a class="badge" href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->catid, $item->language)); ?>"><?php echo T::e($item->category_title); ?></a>
        <time datetime="<?php echo HTMLHelper::_('date', $item->publish_up, 'c'); ?>"><?php echo HTMLHelper::_('date', $item->publish_up, 'd M Y'); ?></time>
    </p>

    <?php if ($image) : ?>
        <figure class="news-article__image">
            <img src="<?php echo T::e($image->url); ?>" alt="<?php echo T::e($alt); ?>"
                <?php echo !empty($image->attributes['width']) ? 'width="' . (int) $image->attributes['width'] . '" height="' . (int) $image->attributes['height'] . '"' : ''; ?>>
        </figure>
    <?php endif; ?>

    <div class="prose news-article__body"><?php echo $item->text; ?></div>

    <?php echo $item->event->afterDisplayContent; ?>

    <?php if ($root) : ?>
        <p class="back-link">
            <a class="link-arrow" href="<?php echo Route::_(RouteHelper::getCategoryRoute($root->id, $root->language)); ?>">
                <?php echo T::icon('chevron-left'); ?> <?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL_NEWS_BACK'); ?>
            </a>
        </p>
    <?php endif; ?>
</article>
