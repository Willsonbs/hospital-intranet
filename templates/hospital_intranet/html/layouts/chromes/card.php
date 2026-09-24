<?php

/**
 * Module chrome "card": painel branco elevado (Acesso rápido, sidebar).
 * Formato do título: ver html/layouts/hospital/section-head.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$module = $displayData['module'];
$params = $displayData['params'];

if ((string) $module->content === '') {
	return;
}

$headerId = 'mod-' . (int) $module->id . '-title';
$class    = htmlspecialchars((string) $params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
?>
<section class="hi-card hi-module hi-module--card <?php echo $class; ?>"<?php echo $module->showtitle ? ' aria-labelledby="' . $headerId . '"' : ''; ?>>
	<?php if ($module->showtitle) : ?>
		<?php echo LayoutHelper::render('hospital.section-head', ['module' => $module, 'id' => $headerId]); ?>
	<?php endif; ?>
	<?php echo $module->content; ?>
</section>
