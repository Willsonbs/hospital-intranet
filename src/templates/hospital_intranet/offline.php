<?php

/**
 * Site em manutenção. Mantém o formulário de login padrão do Joomla para que
 * administradores possam ver o site durante a manutenção.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

require __DIR__ . '/partials/bootstrap.php';

$message = match ((int) $app->get('display_offline_message', 1)) {
    1       => trim((string) $app->get('offline_message', '')),
    2       => Text::_('JOFFLINE_MESSAGE'),
    default => '',
};
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="site is-offline">
    <main class="offline">
        <div class="card offline__card">
            <p class="brand brand--on-light">
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M9.5 3h5v6.5H21v5h-6.5V21h-5v-6.5H3v-5h6.5z"/></svg>
                </span>
                <span class="brand__text">
                    <span class="brand__name"><?php echo $brandName; ?></span>
                    <span class="brand__subtitle"><?php echo $brandSubtitle; ?></span>
                </span>
            </p>
            <h1 class="offline__title"><?php echo Text::_('TPL_HOSPITAL_INTRANET_OFFLINE_TITLE'); ?></h1>
            <?php if ($message !== '') : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <jdoc:include type="message" />

            <details class="offline__login">
                <summary><?php echo Text::_('TPL_HOSPITAL_INTRANET_OFFLINE_ADMIN_LOGIN'); ?></summary>
                <form action="<?php echo Route::_('index.php', true); ?>" method="post" class="stack">
                    <div class="field">
                        <label class="field__label" for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                        <input class="input" name="username" id="username" type="text" autocomplete="username" required>
                    </div>
                    <div class="field">
                        <label class="field__label" for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                        <input class="input" name="password" id="password" type="password" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn--primary"><?php echo Text::_('JLOGIN'); ?></button>
                    <input type="hidden" name="option" value="com_users">
                    <input type="hidden" name="task" value="user.login">
                    <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            </details>
        </div>
    </main>
</body>
</html>
