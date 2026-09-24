<?php

/**
 * Página de erro (404, 500...).
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var \Joomla\CMS\Document\ErrorDocument $this */

$tplPath = Uri::root(true) . '/templates/' . $this->template;
$code    = (int) $this->error->getCode();
$is404   = $code === 404;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $code; ?> — <?php echo htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8'); ?></title>
	<link rel="icon" href="<?php echo $tplPath; ?>/images/logo.svg" type="image/svg+xml">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/variables.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/template.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/components.css">
</head>
<body class="site is-error">
	<div class="hi-top">
		<div class="hi-pagehead">
			<div class="container">
				<p class="hi-eyebrow hi-eyebrow--light">Erro <?php echo $code; ?></p>
				<h1 class="hi-pagehead__title">
					<?php echo $is404 ? Text::_('TPL_HOSPITAL_INTRANET_ERROR_TITLE') : htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
				</h1>
			</div>
		</div>
	</div>
	<main class="container hi-section">
		<div class="hi-card">
			<p><?php echo Text::_('TPL_HOSPITAL_INTRANET_ERROR_TEXT'); ?></p>
			<a class="hi-btn" href="<?php echo $this->baseurl; ?>/"><?php echo Text::_('TPL_HOSPITAL_INTRANET_BACK_HOME'); ?></a>
		</div>
		<?php if ($this->debug) : ?>
			<?php echo $this->renderBacktrace(); ?>
		<?php endif; ?>
	</main>
</body>
</html>
