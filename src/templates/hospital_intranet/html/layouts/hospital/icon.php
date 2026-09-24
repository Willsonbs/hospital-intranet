<?php

/**
 * Ícone do sprite do template (Lucide).
 *
 * Uso: LayoutHelper::render('hospital.icon', ['name' => 'users', 'class' => '...', 'label' => '...'])
 * Sem "label" o ícone é decorativo (aria-hidden); com "label" é anunciado por leitores de tela.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$name  = preg_replace('/[^a-z0-9-]/', '', (string) ($displayData['name'] ?? ''));
$class = trim('icon ' . ($displayData['class'] ?? ''));
$label = (string) ($displayData['label'] ?? '');

static $sprite = null;
$sprite ??= Uri::root(true) . '/media/templates/site/hospital_intranet/images/icons.svg?'
    . Factory::getApplication()->getDocument()->getMediaVersion();

$a11y = $label === ''
    ? 'aria-hidden="true" focusable="false"'
    : 'role="img" aria-label="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '"';
?>
<svg class="<?php echo htmlspecialchars($class, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $a11y; ?>><use href="<?php echo $sprite . '#' . $name; ?>"></use></svg>
