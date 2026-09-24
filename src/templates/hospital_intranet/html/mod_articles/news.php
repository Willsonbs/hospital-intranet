<?php

/**
 * Últimas notícias (mod_articles, categoria Notícias): cards com imagem,
 * categoria, data, título e resumo.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\HTML\HTMLHelper;
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
        'text' => Text::_('TPL_HOSPITAL_INTRANET_ALL_NEWS'),
    ];
}

$headingTag = $module->showtitle ? 'h3' : 'h2';
?>
<ul class="card-grid">
    <?php foreach ($list as $item) :
        $image = T::introImage($item);
        ?>
        <li>
            <article class="news-card is-clickable">
                <?php if ($image) : ?>
                    <figure class="news-card__media">
                        <img src="<?php echo T::e($image['url']); ?>" alt="<?php echo T::e($image['alt']); ?>" loading="lazy" decoding="async"
                            <?php echo $image['width'] ? 'width="' . $image['width'] . '" height="' . $image['height'] . '"' : ''; ?>>
                    </figure>
                <?php else : ?>
                    <div class="news-card__media news-card__media--empty tone-teal" aria-hidden="true"><?php echo T::icon('newspaper'); ?></div>
                <?php endif; ?>

                <div class="news-card__body">
                    <p class="news-card__meta">
                        <span class="badge"><?php echo T::e($item->category_title); ?></span>
                        <time datetime="<?php echo HTMLHelper::_('date', $item->publish_up, 'c'); ?>">
                            <?php echo HTMLHelper::_('date', $item->publish_up, 'd M Y'); ?>
                        </time>
                    </p>
                    <<?php echo $headingTag; ?> class="news-card__title">
                        <a class="stretched-link" href="<?php echo T::e($item->link); ?>"><?php echo T::e($item->title); ?></a>
                    </<?php echo $headingTag; ?>>
                    <p class="news-card__excerpt"><?php echo T::e(T::excerpt($item->introtext, (int) $params->get('introtext_limit', 140))); ?></p>
                    <p class="news-card__more" aria-hidden="true">
                        <span class="link-arrow"><?php echo Text::_('TPL_HOSPITAL_INTRANET_READ_MORE'); ?> <?php echo T::icon('arrow-right'); ?></span>
                    </p>
                </div>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
