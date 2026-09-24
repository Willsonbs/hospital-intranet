<?php

/**
 * Template da intranet do Hospital Santa Aurora.
 *
 * Página inicial: header sobre o hero (saudação + avisos), seguido das posições
 * quick-access → news → protocols → events.
 * Demais páginas: header sobre uma faixa com o título da página.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

require __DIR__ . '/partials/bootstrap.php';

$input     = $app->getInput();
$pageclass = $active !== null ? $active->getParams()->get('pageclass_sfx', '') : '';
$hasAside  = $this->countModules('sidebar');
$homeSections = ['quick-access', 'news', 'protocols', 'events'];

if ($isHome) {
    $wa->useScript('template.hospital_intranet.greeting');
}

$bodyClass = implode(' ', array_filter([
    'site',
    $isHome ? 'is-home' : 'is-inner',
    'option-' . $input->getCmd('option'),
    'view-' . $input->getCmd('view'),
    $hasAside ? 'has-sidebar' : '',
    $pageclass,
]));
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="<?php echo htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>">
    <a class="skip-link" href="#conteudo"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SKIP_TO_CONTENT'); ?></a>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main id="conteudo" class="site-main" tabindex="-1">
        <div class="masthead<?php echo $isHome ? ' masthead--home' : ''; ?>"<?php echo $heroStyle; ?>>
            <?php if ($isHome) : ?>
                <div class="container hero">
                    <?php if ($this->countModules('hero')) : ?>
                        <jdoc:include type="modules" name="hero" style="none" />
                    <?php else : ?>
                        <p class="eyebrow eyebrow--on-dark"><?php echo htmlspecialchars($params->get('heroEyebrow', ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <h1 class="hero__title" data-greeting
                            data-morning="<?php echo Text::_('TPL_HOSPITAL_INTRANET_GREETING_MORNING'); ?>"
                            data-afternoon="<?php echo Text::_('TPL_HOSPITAL_INTRANET_GREETING_AFTERNOON'); ?>"
                            data-evening="<?php echo Text::_('TPL_HOSPITAL_INTRANET_GREETING_EVENING'); ?>"><?php echo $greeting; ?></h1>
                        <p class="hero__text"><?php echo htmlspecialchars($params->get('heroText', ''), ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>

                    <?php // Avisos importantes ficam no hero: visíveis sem rolar a página ?>
                    <?php if ($this->countModules('alerts')) : ?>
                        <div class="hero__alerts">
                            <jdoc:include type="modules" name="alerts" style="none" />
                        </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="container page-banner">
                    <?php if ($this->countModules('breadcrumbs')) : ?>
                        <jdoc:include type="modules" name="breadcrumbs" style="none" />
                    <?php endif; ?>
                    <h1 class="page-banner__title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
            <?php endif; ?>
        </div>

        <div class="container site-main__content">
            <jdoc:include type="message" />

            <?php if ($isHome) : ?>
                <?php foreach ($homeSections as $position) : ?>
                    <?php if ($this->countModules($position)) : ?>
                        <div class="home-section home-section--<?php echo $position; ?>">
                            <jdoc:include type="modules" name="<?php echo $position; ?>" style="section" />
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="page-layout">
                    <div class="page-layout__main">
                        <jdoc:include type="modules" name="main-top" style="section" />
                        <jdoc:include type="component" />
                        <jdoc:include type="modules" name="main-bottom" style="section" />
                    </div>
                    <?php if ($hasAside) : ?>
                        <aside class="page-layout__aside">
                            <jdoc:include type="modules" name="sidebar" style="card" />
                        </aside>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
