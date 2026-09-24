<?php

/**
 * Chrome "card": módulo dentro de um card (barra lateral).
 */

defined('_JEXEC') or die;

$module = $displayData['module'];
$params = $displayData['params'];

if ((string) $module->content === '') {
    return;
}

$headingTag = htmlspecialchars($params->get('header_tag', 'h2'), ENT_QUOTES, 'UTF-8');
$class      = trim('card card--module ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'));
?>
<section class="<?php echo $class; ?>">
    <?php if ($module->showtitle) : ?>
        <<?php echo $headingTag; ?> class="card__title"><?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?></<?php echo $headingTag; ?>>
    <?php endif; ?>
    <?php echo $module->content; ?>
</section>
