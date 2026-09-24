<?php

/**
 * Chrome "footercol": coluna do footer com título opcional.
 */

defined('_JEXEC') or die;

$module = $displayData['module'];

if ((string) $module->content === '') {
    return;
}
?>
<section class="footer-col">
    <?php if ($module->showtitle) : ?>
        <h2 class="footer-col__title"><?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php endif; ?>
    <?php echo $module->content; ?>
</section>
