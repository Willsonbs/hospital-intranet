<?php

/**
 * Saída "somente componente" (tmpl=component): modais, impressão.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

/** @var \Joomla\CMS\Document\HtmlDocument $this */

$tplPath = Uri::root(true) . '/templates/' . $this->template;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<link rel="stylesheet" href="<?php echo Uri::root(true); ?>/media/system/css/joomla-fontawesome.min.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/variables.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/template.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/components.css">
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="contentpane">
	<main class="container hi-section">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</main>
</body>
</html>
