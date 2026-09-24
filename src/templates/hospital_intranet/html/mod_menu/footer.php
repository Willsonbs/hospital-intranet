<?php

/**
 * Menu simples em lista, para colunas do footer ("Links úteis").
 * Mostra apenas o 1º nível.
 */

defined('_JEXEC') or die;

use Joomla\Filter\OutputFilter;

$startLevel = (int) $params->get('startLevel', 1);
?>
<ul class="footer-links">
<?php foreach ($list as $item) : ?>
    <?php if ((int) $item->level !== $startLevel || in_array($item->type, ['separator', 'heading'], true)) {
        continue;
    } ?>
    <li>
        <a href="<?php echo OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)); ?>"<?php echo $item->browserNav == 1 ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo $item->title; ?></a>
    </li>
<?php endforeach; ?>
</ul>
