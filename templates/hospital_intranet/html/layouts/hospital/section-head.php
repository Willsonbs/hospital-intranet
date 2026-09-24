<?php

/**
 * Cabeçalho de seção: rótulo + título + link opcional "Ver todas".
 *
 * O título do módulo aceita até três partes separadas por "|":
 *   "Fique por dentro | Últimas notícias | /noticias"
 *    rótulo              título             link "Ver todas"
 *
 * @var array $displayData ['module' => object, 'id' => string]
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$module  = $displayData['module'];
$id      = $displayData['id'];
$parts   = array_map('trim', explode('|', $module->title, 3));
$eyebrow = count($parts) > 1 ? $parts[0] : '';
$title   = count($parts) > 1 ? $parts[1] : $parts[0];
$link    = $parts[2] ?? '';

if ($link !== '' && str_starts_with($link, 'index.php')) {
	$link = Route::_($link);
} elseif ($link !== '' && !preg_match('#^(https?://|/)#i', $link)) {
	$link = '';
}

$esc = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<div class="hi-section-head">
	<div>
		<?php if ($eyebrow !== '') : ?>
			<p class="hi-eyebrow"><?php echo $esc($eyebrow); ?></p>
		<?php endif; ?>
		<h2 id="<?php echo $esc($id); ?>" class="hi-section-title"><?php echo $esc($title); ?></h2>
	</div>
	<?php if ($link !== '') : ?>
		<a class="hi-link-more" href="<?php echo $esc($link); ?>">
			<?php echo Text::_('TPL_HOSPITAL_INTRANET_SEE_ALL'); ?><span class="visually-hidden">: <?php echo $esc($title); ?></span>
			<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
		</a>
	<?php endif; ?>
</div>
