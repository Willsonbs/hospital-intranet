<?php

/**
 * Notícias: abas por categoria (Institucional, Pessoas…), cards e paginação.
 *
 * @var \Joomla\Component\Content\Site\View\Category\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$items    = T::categoryItems(array_merge($this->lead_items, $this->intro_items, $this->link_items), $this->params);
$children = T::filterCategories($this->category, $this->params);
$current  = (int) $this->category->id;
$root     = $this->category;

while ((int) $root->level > 1) {
    $root = $root->getParent();
}
?>
<div class="page-section">
    <?php if ($children) : ?>
        <nav class="chips" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_NEWS_CATEGORIES'); ?>">
            <a class="chip" href="<?php echo Route::_(RouteHelper::getCategoryRoute($root->id, $root->language)); ?>"<?php echo $current === (int) $root->id ? ' aria-current="page"' : ''; ?>>
                <?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL'); ?>
            </a>
            <?php foreach ($children as $child) : ?>
                <a class="chip" href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>"<?php echo $current === (int) $child->id ? ' aria-current="page"' : ''; ?>>
                    <?php echo T::e($child->title); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php if ($items) : ?>
        <ul class="card-grid">
            <?php foreach ($items as $item) : ?>
                <li><?php echo LayoutHelper::render('hospital.news-card', ['item' => $item, 'heading' => 'h2']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="empty-state">
            <?php echo T::icon('newspaper'); ?>
            <p><?php echo Text::_('TPL_HOSPITAL_INTRANET_NEWS_EMPTY'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($this->pagination->pagesTotal > 1) : ?>
        <div class="pager">
            <p class="pager__counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif; ?>
</div>
