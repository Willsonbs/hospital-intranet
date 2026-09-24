<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  tpl_hospital_intranet
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var \Joomla\CMS\Document\HtmlDocument $this */

$app    = Factory::getApplication();
$menu   = $app->getMenu();
$active = $menu->getActive();
$params = $this->params;

$isHome  = $active && $active->home;
$tplPath = Uri::root(true) . '/templates/' . $this->template;
$version = '0.4.0';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$this->setMetaData('theme-color', '#075B63');

$esc = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$logoFile  = $params->get('logoFile');
$heroImage = $params->get('heroImage');
$heroStyle = $heroImage ? ' style="--hero-image:url(\'' . $esc(HTMLHelper::_('cleanImageURL', $heroImage)->url) . '\')"' : '';

$pageClass = $active ? $active->getParams()->get('pageclass_sfx', '') : '';
$bodyClass = 'site ' . ($isHome ? 'is-home' : 'is-inner') . ' ' . $esc($pageClass);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<link rel="icon" href="<?php echo $tplPath; ?>/images/logo.svg" type="image/svg+xml">
	<link rel="stylesheet" href="<?php echo Uri::root(true); ?>/media/system/css/joomla-fontawesome.min.css">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/variables.css?v=<?php echo $version; ?>">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/template.css?v=<?php echo $version; ?>">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/components.css?v=<?php echo $version; ?>">
	<link rel="stylesheet" href="<?php echo $tplPath; ?>/css/responsive.css?v=<?php echo $version; ?>">
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
	<script type="module" src="<?php echo $tplPath; ?>/js/template.js?v=<?php echo $version; ?>"></script>
</head>
<body class="<?php echo trim($bodyClass); ?>">
	<a class="skip-link" href="#conteudo"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SKIP_TO_CONTENT'); ?></a>

	<div class="hi-top<?php echo $isHome ? ' hi-top--hero' : ''; ?>"<?php echo $heroStyle; ?>>
		<header class="hi-header" role="banner">
			<div class="container hi-header__inner">
				<a class="hi-brand" href="<?php echo $this->baseurl; ?>/">
					<?php if ($logoFile) : ?>
						<?php echo HTMLHelper::_('image', $logoFile, '', ['class' => 'hi-brand__logo'], false, 0); ?>
					<?php else : ?>
						<span class="hi-brand__mark" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="28" height="28" focusable="false"><path d="M8.5 2h7v6.5H22v7h-6.5V22h-7v-6.5H2v-7h6.5z" fill="currentColor"/></svg>
						</span>
					<?php endif; ?>
					<span class="hi-brand__text">
						<strong><?php echo $esc($params->get('brandLine1', 'Intranet')); ?></strong>
						<span><?php echo $esc($params->get('brandLine2', 'Hospitalar')); ?></span>
					</span>
				</a>

				<button class="hi-nav-toggle" type="button" aria-expanded="false" aria-controls="hi-nav">
					<i class="fa-solid fa-bars" aria-hidden="true"></i>
					<span class="visually-hidden"><?php echo Text::_('TPL_HOSPITAL_INTRANET_MENU'); ?></span>
				</button>

				<nav id="hi-nav" class="hi-nav" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_MAIN_NAV'); ?>">
					<jdoc:include type="modules" name="mainmenu" style="none" />
					<?php if ($this->countModules('header')) : ?>
						<div class="hi-header__tools">
							<jdoc:include type="modules" name="header" style="none" />
						</div>
					<?php endif; ?>
				</nav>
			</div>
		</header>

		<?php if ($isHome) : ?>
			<section class="hi-hero" aria-labelledby="hi-greeting">
				<div class="container">
					<p class="hi-eyebrow hi-eyebrow--light"><?php echo $esc($params->get('heroEyebrow', 'Intranet corporativa')); ?></p>
					<h1 id="hi-greeting" class="hi-hero__title" data-greeting>
						<?php echo Text::_('TPL_HOSPITAL_INTRANET_GREETING_DEFAULT'); ?>
					</h1>
					<p class="hi-hero__subtitle"><?php echo $esc($params->get('heroSubtitle')); ?></p>
					<jdoc:include type="modules" name="hero" style="none" />
				</div>
			</section>
		<?php else : ?>
			<div class="hi-pagehead">
				<div class="container">
					<p class="hi-eyebrow hi-eyebrow--light"><?php echo $esc($params->get('heroEyebrow', 'Intranet corporativa')); ?></p>
					<p class="hi-pagehead__title"><?php echo $esc($active ? $active->title : $this->getTitle()); ?></p>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<main id="conteudo" class="hi-main" tabindex="-1">
		<?php if ($this->countModules('quick-access')) : ?>
			<div class="container hi-overlap">
				<jdoc:include type="modules" name="quick-access" style="card" />
			</div>
		<?php endif; ?>

		<?php if ($this->countModules('alerts')) : ?>
			<div class="container hi-section hi-section--tight">
				<jdoc:include type="modules" name="alerts" style="none" />
			</div>
		<?php endif; ?>

		<?php foreach (['news', 'protocols', 'events'] as $position) : ?>
			<?php if ($this->countModules($position)) : ?>
				<div class="container hi-section hi-section--<?php echo $position; ?>">
					<jdoc:include type="modules" name="<?php echo $position; ?>" style="section" />
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if (!$isHome) : ?>
			<div class="container hi-section hi-content<?php echo $this->countModules('sidebar') ? ' hi-content--sidebar' : ''; ?>">
				<div class="hi-content__main">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
				<?php if ($this->countModules('sidebar')) : ?>
					<aside class="hi-content__aside">
						<jdoc:include type="modules" name="sidebar" style="card" />
					</aside>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="container"><jdoc:include type="message" /></div>
		<?php endif; ?>
	</main>

	<footer class="hi-footer" role="contentinfo">
		<div class="container hi-footer__grid">
			<div class="hi-footer__brand">
				<p class="hi-footer__name"><?php echo $esc($params->get('institutionName', 'Hospital Santa Aurora')); ?></p>
				<p class="hi-footer__tag"><?php echo $esc($params->get('heroEyebrow', 'Intranet corporativa')); ?></p>
			</div>
			<?php if ($this->countModules('footer-menu')) : ?>
				<nav class="hi-footer__col" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_FOOTER_NAV'); ?>">
					<jdoc:include type="modules" name="footer-menu" style="footer" />
				</nav>
			<?php endif; ?>
			<?php if ($this->countModules('footer')) : ?>
				<div class="hi-footer__col">
					<jdoc:include type="modules" name="footer" style="footer" />
				</div>
			<?php endif; ?>
		</div>
		<div class="container hi-footer__bottom">
			<p>&copy; <?php echo date('Y'); ?> <?php echo $esc($params->get('institutionName', 'Hospital Santa Aurora')); ?></p>
		</div>
	</footer>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
