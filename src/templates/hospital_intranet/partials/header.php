<?php

/**
 * Header: marca, menu principal, busca e botão do menu mobile.
 * Variáveis vindas de partials/bootstrap.php.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$hasSearchModule = $this->countModules('search');
?>
<header class="site-header" data-site-header>
    <div class="container site-header__inner">
        <a class="brand" href="<?php echo $this->baseurl; ?>/">
            <?php if ($logoUrl) : ?>
                <img class="brand__logo" src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="44" height="44">
            <?php else : ?>
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M9.5 3h5v6.5H21v5h-6.5V21h-5v-6.5H3v-5h6.5z"/></svg>
                </span>
            <?php endif; ?>
            <span class="brand__text">
                <span class="brand__name"><?php echo $brandName; ?></span>
                <span class="brand__subtitle"><?php echo $brandSubtitle; ?></span>
            </span>
        </a>

        <nav class="site-nav" id="site-nav" aria-label="<?php echo Text::_('TPL_HOSPITAL_INTRANET_MAIN_NAV'); ?>" data-site-nav>
            <jdoc:include type="modules" name="mainmenu" style="none" />
        </nav>

        <div class="site-header__actions">
            <button type="button" class="icon-btn" aria-expanded="false" aria-controls="site-search" data-search-toggle>
                <?php echo LayoutHelper::render('hospital.icon', ['name' => 'search']); ?>
                <span class="sr-only"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SEARCH_OPEN'); ?></span>
            </button>
            <button type="button" class="icon-btn nav-toggle" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
                <?php echo LayoutHelper::render('hospital.icon', ['name' => 'menu', 'class' => 'nav-toggle__open']); ?>
                <?php echo LayoutHelper::render('hospital.icon', ['name' => 'x', 'class' => 'nav-toggle__close']); ?>
                <span class="sr-only"><?php echo Text::_('TPL_HOSPITAL_INTRANET_MENU_TOGGLE'); ?></span>
            </button>
        </div>
    </div>

    <div class="site-search" id="site-search" data-site-search>
        <div class="container">
            <?php if ($hasSearchModule) : ?>
                <jdoc:include type="modules" name="search" style="none" />
            <?php else : ?>
                <?php // Fallback até o módulo de busca (Smart Search) ser configurado ?>
                <form class="search-field search-field--lg" role="search" action="<?php echo Route::_('index.php?option=com_finder&view=search'); ?>" method="get">
                    <label class="sr-only" for="site-search-q"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SEARCH_LABEL'); ?></label>
                    <?php echo LayoutHelper::render('hospital.icon', ['name' => 'search', 'class' => 'search-field__icon']); ?>
                    <input class="input" type="search" id="site-search-q" name="q" placeholder="<?php echo Text::_('TPL_HOSPITAL_INTRANET_SEARCH_PLACEHOLDER'); ?>" autocomplete="off">
                    <button class="btn btn--primary" type="submit"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SEARCH_SUBMIT'); ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>
