<?php

/**
 * Página exibida com o site em manutenção (modo offline).
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/** @var \Joomla\CMS\Document\HtmlDocument $this */

$app     = Factory::getApplication();
$tplPath = Uri::root(true) . '/templates/' . $this->template;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/variables.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/template.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/components.css">
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="site is-offline">
	<div class="hi-top">
		<div class="hi-pagehead">
			<div class="container">
				<p class="hi-eyebrow hi-eyebrow--light"><?php echo htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8'); ?></p>
				<h1 class="hi-pagehead__title">Em manutenção</h1>
			</div>
		</div>
	</div>
	<main class="container hi-section">
		<div class="hi-card">
			<jdoc:include type="message" />
			<p><?php echo $app->get('offline_message', 'A intranet está temporariamente em manutenção.'); ?></p>
		</div>
	</main>
</body>
</html>
