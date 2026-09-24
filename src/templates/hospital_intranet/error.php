<?php

/**
 * Página de erro (404, 403, 500...), com header e footer do site.
 *
 * @var Joomla\CMS\Document\ErrorDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

require __DIR__ . '/partials/bootstrap.php';

$errorCode = (int) $this->error->getCode();
$isNotFound = $errorCode === 404;

// Em erros graves a aplicação pode não estar completa: não renderiza módulos
$renderModules = $app->getIdentity() && $app->getLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="site is-inner is-error error-<?php echo $errorCode; ?>">
    <a class="skip-link" href="#conteudo"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SKIP_TO_CONTENT'); ?></a>

    <?php if ($renderModules) : ?>
        <?php require __DIR__ . '/partials/header.php'; ?>
    <?php endif; ?>

    <main id="conteudo" class="site-main" tabindex="-1">
        <div class="masthead">
            <div class="container page-banner">
                <p class="eyebrow eyebrow--on-dark"><?php echo Text::sprintf('TPL_HOSPITAL_INTRANET_ERROR_CODE', $errorCode); ?></p>
                <h1 class="page-banner__title">
                    <?php echo Text::_($isNotFound ? 'TPL_HOSPITAL_INTRANET_ERROR_404_TITLE' : 'TPL_HOSPITAL_INTRANET_ERROR_TITLE'); ?>
                </h1>
            </div>
        </div>

        <div class="container site-main__content">
            <div class="card error-card">
                <p><?php echo Text::_($isNotFound ? 'TPL_HOSPITAL_INTRANET_ERROR_404_TEXT' : 'TPL_HOSPITAL_INTRANET_ERROR_TEXT'); ?></p>
                <p class="error-card__actions">
                    <a class="btn btn--primary" href="<?php echo $this->baseurl; ?>/">
                        <?php echo LayoutHelper::render('hospital.icon', ['name' => 'house']); ?>
                        <?php echo Text::_('TPL_HOSPITAL_INTRANET_ERROR_HOME'); ?>
                    </a>
                </p>
                <?php if ($renderModules && $this->countModules('error-404') && $isNotFound) : ?>
                    <jdoc:include type="modules" name="error-404" style="none" />
                <?php endif; ?>

                <?php if ($this->debug) : ?>
                    <details class="error-card__debug">
                        <summary><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></summary>
                        <?php echo $this->renderBacktrace(); ?>
                    </details>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php if ($renderModules) : ?>
        <?php require __DIR__ . '/partials/footer.php'; ?>
    <?php endif; ?>
</body>
</html>
