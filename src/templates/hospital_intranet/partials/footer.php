<?php

/**
 * Footer institucional. Variáveis vindas de partials/bootstrap.php.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$footerTagline = htmlspecialchars($params->get('footerTagline', ''), ENT_QUOTES, 'UTF-8');
$supportTitle  = htmlspecialchars($params->get('supportTitle', ''), ENT_QUOTES, 'UTF-8');
$supportText   = nl2br(htmlspecialchars($params->get('supportText', ''), ENT_QUOTES, 'UTF-8'));
$year          = Factory::getDate()->format('Y');
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <p class="site-footer__name"><?php echo $brandName . ' ' . $brandSubtitle; ?></p>
            <?php if ($footerTagline) : ?>
                <p class="site-footer__tagline"><?php echo $footerTagline; ?></p>
            <?php endif; ?>
        </div>

        <?php if ($this->countModules('footer-menu')) : ?>
            <jdoc:include type="modules" name="footer-menu" style="footercol" />
        <?php endif; ?>

        <?php if ($supportTitle || $supportText) : ?>
            <section class="footer-col">
                <?php if ($supportTitle) : ?>
                    <h2 class="footer-col__title"><?php echo $supportTitle; ?></h2>
                <?php endif; ?>
                <p><?php echo $supportText; ?></p>
            </section>
        <?php endif; ?>

        <?php if ($this->countModules('footer')) : ?>
            <jdoc:include type="modules" name="footer" style="footercol" />
        <?php endif; ?>
    </div>

    <div class="container site-footer__bottom">
        <p><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_COPYRIGHT', $year, $brandName . ' ' . $brandSubtitle); ?></p>
    </div>
</footer>
