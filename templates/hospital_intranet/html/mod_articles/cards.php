<?php

/**
 * Override do mod_articles — layout "Cards" (NewsCard).
 * Selecione em: Módulo → Avançado → Layout → "Cards".
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var \stdClass[] $list */

if (!$list) {
	return;
}

$items = $grouped ? array_merge(...array_values($list)) : $list;
?>
<ul class="hi-news-grid">
	<?php foreach ($items as $item) : ?>
		<li>
			<?php echo LayoutHelper::render('hospital.news-card', [
				'item'    => $item,
				'link'    => $item->link,
				'heading' => $module->showtitle ? 'h3' : 'h2',
				'limit'   => (int) $params->get('introtext_limit', 140),
				'idBase'  => 'news-' . (int) $module->id,
			]); ?>
		</li>
	<?php endforeach; ?>
</ul>
