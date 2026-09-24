<?php

/**
 * Somente o conteúdo do componente (impressão, modais, ?tmpl=component).
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

require __DIR__ . '/partials/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body class="site is-component">
    <main class="container site-main">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
    </main>
</body>
</html>
