<?php

/**
 * Acesso rápido da home (mod_articles, categoria Sistemas, só artigos em destaque).
 * Campos: sis-url, sis-icone, sis-cor, sis-nova-aba. Descrição = texto de introdução.
 * A ordem é a de "Artigos em destaque" no painel.
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

use HospitalIntranetTemplate as T;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$catids = array_filter((array) $params->get('catid', []));

if ($catids) {
    $module->sectionLink = [
        'url'  => Route::_(RouteHelper::getCategoryRoute((int) reset($catids))),
        'text' => Text::_('TPL_HOSPITAL_INTRANET_ALL_SYSTEMS'),
    ];
}

// Diretório de ramais: item "Ramais" do menu principal (§13.9 da especificação)
$ramais = $app->getMenu()->getItems(['alias', 'menutype'], ['ramais', 'mainmenu'], true);
?>
<?php if ($list) : ?>
    <ul class="system-list">
        <?php foreach ($list as $item) :
            $url = (string) T::raw($item, 'sis-url');

            if ($url === '') {
                continue;
            }

            $newTab = T::raw($item, 'sis-nova-aba', '1') === '1';
            ?>
            <li>
                <a class="system-item" href="<?php echo T::e($url); ?>"<?php echo $newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <span class="icon-tile <?php echo T::tone((string) T::raw($item, 'sis-cor', 'teal')); ?>">
                        <?php echo T::icon((string) T::raw($item, 'sis-icone', 'layout-grid')); ?>
                    </span>
                    <span class="system-item__body">
                        <span class="system-item__title"><?php echo T::e($item->title); ?></span>
                        <span class="system-item__desc"><?php echo T::e(T::excerpt($item->introtext, 90)); ?></span>
                    </span>
                    <?php if ($newTab) : ?>
                        <span class="sr-only"><?php echo Text::_('TPL_HOSPITAL_INTRANET_NEW_WINDOW'); ?></span>
                    <?php endif; ?>
                    <?php echo T::icon('chevron-right', 'system-item__chevron'); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($ramais) : ?>
    <div class="directory-strip">
        <span class="icon-tile tone-teal"><?php echo T::icon('phone'); ?></span>
        <div class="directory-strip__body">
            <p class="directory-strip__title"><?php echo T::e($ramais->title); ?></p>
            <p class="directory-strip__text"><?php echo Text::_('TPL_HOSPITAL_INTRANET_DIRECTORY_TEXT'); ?></p>
        </div>
        <a class="btn btn--primary" href="<?php echo Route::_('index.php?Itemid=' . (int) $ramais->id); ?>">
            <?php echo Text::_('TPL_HOSPITAL_INTRANET_DIRECTORY_BUTTON'); ?>
            <?php echo T::icon('arrow-right'); ?>
        </a>
    </div>
<?php endif;
