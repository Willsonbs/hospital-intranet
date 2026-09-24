<?php

/**
 * NewsCard: imagem, categoria, data, título, resumo e "Leia mais". O card inteiro é clicável.
 *
 * @var array $displayData [
 *     'item'    => object  artigo do com_content (title, introtext, images, category_title, publish_up),
 *     'link'    => string  URL já roteada,
 *     'heading' => string  h2|h3,
 *     'limit'   => int     tamanho máximo do resumo,
 *     'idBase'  => string  prefixo único para o id do título,
 * ]
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$item    = $displayData['item'];
$heading = in_array($displayData['heading'] ?? 'h3', ['h2', 'h3', 'h4'], true) ? $displayData['heading'] : 'h3';
$limit   = (int) ($displayData['limit'] ?? 140) ?: 140;
$titleId = ($displayData['idBase'] ?? 'news') . '-' . (int) $item->id;

$images  = is_string($item->images ?? null) ? json_decode($item->images) : ($item->images ?? null);
$image   = $images->image_intro ?? '';
$alt     = $images->image_intro_alt ?? '';
$summary = HTMLHelper::_('string.truncate', trim(strip_tags($item->introtext ?? '')), $limit, true, false);

$esc = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<article class="hi-news-card" aria-labelledby="<?php echo $titleId; ?>">
	<div class="hi-news-card__media">
		<?php if ($image) : ?>
			<img src="<?php echo $esc(HTMLHelper::_('cleanImageURL', $image)->url); ?>" alt="<?php echo $esc($alt); ?>" loading="lazy" decoding="async">
		<?php else : ?>
			<span class="hi-news-card__placeholder" aria-hidden="true"><i class="fa-solid fa-newspaper"></i></span>
		<?php endif; ?>
	</div>
	<div class="hi-news-card__body">
		<p class="hi-news-card__meta">
			<span class="hi-news-card__category"><?php echo $esc($item->category_title ?? ''); ?></span>
			<time datetime="<?php echo HTMLHelper::_('date', $item->publish_up, 'c'); ?>"><?php echo HTMLHelper::_('date', $item->publish_up, 'd M Y'); ?></time>
		</p>
		<<?php echo $heading; ?> id="<?php echo $titleId; ?>" class="hi-news-card__title">
			<a href="<?php echo $esc($displayData['link']); ?>" class="hi-news-card__link"><?php echo $esc($item->title); ?></a>
		</<?php echo $heading; ?>>
		<?php if ($summary !== '') : ?>
			<p class="hi-news-card__summary"><?php echo $esc($summary); ?></p>
		<?php endif; ?>
		<span class="hi-news-card__more" aria-hidden="true">
			<?php echo Text::_('TPL_HOSPITAL_INTRANET_READ_MORE'); ?> <i class="fa-solid fa-arrow-right"></i>
		</span>
	</div>
</article>
