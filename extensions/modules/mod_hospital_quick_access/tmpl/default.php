<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_quick_access
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \stdClass[] $items */

$esc = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<ul class="hi-shortcuts">
	<?php foreach ($items as $item) : ?>
		<li>
			<a class="hi-shortcut" href="<?php echo $esc($item->href); ?>"<?php echo $item->newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<span class="hi-shortcut__icon tone-<?php echo $item->tone; ?>" aria-hidden="true">
					<i class="<?php echo $esc($item->icon); ?>"></i>
				</span>
				<span class="hi-shortcut__body">
					<span class="hi-shortcut__title"><?php echo $esc($item->title); ?></span>
					<?php if ($item->description !== '') : ?>
						<span class="hi-shortcut__desc"><?php echo $esc($item->description); ?></span>
					<?php endif; ?>
				</span>
				<?php if ($item->newTab) : ?>
					<span class="visually-hidden"><?php echo Text::_('MOD_HOSPITAL_QUICK_ACCESS_OPENS_NEW_TAB'); ?></span>
				<?php endif; ?>
				<i class="fa-solid fa-chevron-right hi-shortcut__arrow" aria-hidden="true"></i>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
