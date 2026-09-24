<?php

/**
 * Override do blog de categoria (página Notícias): filtro por categoria + grade de NewsCards.
 *
 * Os filtros são as subcategorias da categoria configurada no item de menu
 * (ex.: Notícias → Institucional, Pessoas, Saúde e Bem-estar...).
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Content\Site\View\Category\HtmlView $this */

$app       = Factory::getApplication();
$active    = $app->getMenu()->getActive();
$rootId    = (int) ($active->query['id'] ?? $this->category->id);
$root      = Categories::getInstance('Content')->get($rootId);
$filters   = $root ? $root->getChildren() : [];
$currentId = (int) $this->category->id;
$items     = array_merge($this->lead_items, $this->intro_items);
$esc       = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<div class="hi-blog">
	<?php if ($filters) : ?>
		<nav class="hi-filters" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_FILTER_BY_CATEGORY'); ?>">
			<ul>
				<li>
					<a class="hi-chip" href="<?php echo Route::_('index.php?Itemid=' . (int) $active->id); ?>"<?php echo $currentId === $rootId ? ' aria-current="page"' : ''; ?>>
						<?php echo Text::_('TPL_HOSPITAL_INTRANET_ALL'); ?>
					</a>
				</li>
				<?php foreach ($filters as $child) : ?>
					<li>
						<a class="hi-chip" href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>"<?php echo $currentId === (int) $child->id ? ' aria-current="page"' : ''; ?>>
							<?php echo $esc($child->title); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<?php if ($currentId !== $rootId) : ?>
		<h1 class="hi-blog__title"><?php echo $esc($this->category->title); ?></h1>
	<?php else : ?>
		<h1 class="visually-hidden"><?php echo $esc($this->params->get('page_heading') ?: $this->category->title); ?></h1>
	<?php endif; ?>

	<?php if ($items) : ?>
		<ul class="hi-news-grid">
			<?php foreach ($items as $item) : ?>
				<li>
					<?php echo LayoutHelper::render('hospital.news-card', [
						'item'    => $item,
						'link'    => Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)),
						'heading' => 'h2',
						'limit'   => 160,
						'idBase'  => 'blog',
					]); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="hi-empty">
			<i class="fa-regular fa-newspaper" aria-hidden="true"></i>
			<?php echo Text::_('TPL_HOSPITAL_INTRANET_NO_NEWS'); ?>
		</p>
	<?php endif; ?>

	<?php if ($this->pagination->pagesTotal > 1 && $this->params->def('show_pagination', 2)) : ?>
		<div class="hi-pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
