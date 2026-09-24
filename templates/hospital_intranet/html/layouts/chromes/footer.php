<?php

/**
 * Module chrome "footer": bloco de coluna do rodapé.
 */

defined('_JEXEC') or die;

$module = $displayData['module'];

if ((string) $module->content === '') {
	return;
}
?>
<div class="hi-footer__block">
	<?php if ($module->showtitle) : ?>
		<h2 class="hi-footer__title"><?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?></h2>
	<?php endif; ?>
	<?php echo $module->content; ?>
</div>
