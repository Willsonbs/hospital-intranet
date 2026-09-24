<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_quick_access
 *
 * Layout "catalog" (página Sistemas): SystemCards agrupados por categoria, com âncoras
 * (#assistenciais, #ti...) usadas pelo submenu "Sistemas ▾".
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

/** @var \stdClass[] $items */

$groups = ModHospitalQuickAccessHelper::groupByCategory($items, (string) $params->get('category_order', ''));
$esc    = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<div class="hi-catalog">
	<?php foreach ($groups as $group) : ?>
		<?php $anchor = OutputFilter::stringURLSafe($group['title']); ?>
		<section class="hi-catalog__group" id="<?php echo $esc($anchor); ?>" aria-labelledby="<?php echo $esc($anchor); ?>-title">
			<h2 class="hi-catalog__title" id="<?php echo $esc($anchor); ?>-title"><?php echo $esc($group['title']); ?></h2>
			<ul class="hi-system-grid">
				<?php foreach ($group['items'] as $item) : ?>
					<li class="hi-system-card">
						<span class="hi-shortcut__icon tone-<?php echo $item->tone; ?>" aria-hidden="true">
							<i class="<?php echo $esc($item->icon); ?>"></i>
						</span>
						<h3 class="hi-system-card__title"><?php echo $esc($item->title); ?></h3>
						<?php if ($item->description !== '') : ?>
							<p class="hi-system-card__desc"><?php echo $esc($item->description); ?></p>
						<?php endif; ?>
						<a class="hi-btn hi-btn--ghost hi-system-card__cta" href="<?php echo $esc($item->href); ?>"<?php echo $item->newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<?php echo Text::_('MOD_HOSPITAL_QUICK_ACCESS_OPEN_SYSTEM'); ?>
							<span class="visually-hidden"><?php echo $esc($item->title); ?><?php echo $item->newTab ? ' ' . Text::_('MOD_HOSPITAL_QUICK_ACCESS_OPENS_NEW_TAB') : ''; ?></span>
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>
</div>
