<?php

/**
 * Últimas notícias (mod_articles, categoria Notícias): cards com imagem,
 * categoria, data, título e resumo.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if (!$list) {
    return;
}

$catids = array_filter((array) $params->get('catid', []));

if ($catids) {
    $module->sectionLink = [
        'url'  => Route::_(RouteHelper::getCategoryRoute((int) reset($catids))),
        'text' => Text::_('TPL_HOSPITAL_INTRANET_ALL_NEWS'),
    ];
}

$headingTag = $module->showtitle ? 'h3' : 'h2';
?>
<ul class="card-grid">
    <?php foreach ($list as $item) : ?>
        <li><?php echo LayoutHelper::render('hospital.news-card', ['item' => $item, 'heading' => $headingTag, 'excerpt' => (int) $params->get('introtext_limit', 140)]); ?></li>
    <?php endforeach; ?>
</ul>
