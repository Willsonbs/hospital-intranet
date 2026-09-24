<?php

/**
 * Sistemas e ferramentas (§14): cards agrupados por categoria, com busca.
 * Campos: sis-url, sis-icone, sis-cor, sis-nova-aba. Descrição = texto de introdução.
 *
 * @var \Joomla\Component\Content\Site\View\Category\HtmlView $this
 */

defined('_JEXEC') or die;

use HospitalIntranetTemplate as T;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$this->getDocument()->getWebAssetManager()->useScript('template.hospital_intranet.listfilter');

$search = trim(Factory::getApplication()->getInput()->getString('q', ''));
$groups = [];

foreach (T::categoryItems(array_merge($this->lead_items, $this->intro_items, $this->link_items), $this->params) as $item) {
    if ((string) T::raw($item, 'sis-url') === '') {
        continue;
    }

    $groups[$item->catid] ??= ['title' => $item->category_title, 'items' => []];
    $groups[$item->catid]['items'][] = $item;
}

$visible = 0;
?>
<div class="page-section" data-filter-root>
    <?php if ($this->category->description) : ?>
        <div class="page-intro"><?php echo $this->category->description; ?></div>
    <?php endif; ?>

    <form class="filter-bar" method="get" role="search" data-filter-form>
        <div class="search-field filter-bar__search">
            <label class="sr-only" for="systems-q"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SYSTEMS_SEARCH'); ?></label>
            <?php echo T::icon('search', 'search-field__icon'); ?>
            <input class="input" type="search" id="systems-q" name="q" value="<?php echo T::e($search); ?>"
                   placeholder="<?php echo Text::_('TPL_HOSPITAL_INTRANET_SYSTEMS_SEARCH'); ?>..." autocomplete="off" data-filter="q">
        </div>
        <button class="btn btn--primary filter-bar__submit" type="submit"><?php echo Text::_('TPL_HOSPITAL_INTRANET_SEARCH_SUBMIT'); ?></button>
    </form>

    <?php foreach ($groups as $group) : ?>
        <?php
        $cards = [];

        foreach ($group['items'] as $item) {
            $haystack = T::normalize($item->title . ' ' . strip_tags($item->introtext) . ' ' . $group['title']);
            $cards[]  = [$item, $haystack, $search === '' || T::matches($haystack, $search)];
        }

        $groupVisible = array_filter($cards, static fn ($c) => $c[2]);
        $visible     += \count($groupVisible);
        ?>
        <section class="system-group" data-filter-group<?php echo $groupVisible ? '' : ' hidden'; ?>>
            <h2 class="system-group__title"><?php echo T::e($group['title']); ?></h2>
            <ul class="card-grid">
                <?php foreach ($cards as [$item, $haystack, $match]) :
                    $url    = (string) T::raw($item, 'sis-url');
                    $newTab = T::raw($item, 'sis-nova-aba', '1') === '1';
                    ?>
                    <li class="system-card" data-filter-item data-q="<?php echo T::e($haystack); ?>"<?php echo $match ? '' : ' hidden'; ?>>
                        <span class="icon-tile <?php echo T::tone((string) T::raw($item, 'sis-cor', 'teal')); ?>">
                            <?php echo T::icon((string) T::raw($item, 'sis-icone', 'layout-grid')); ?>
                        </span>
                        <div>
                            <h3 class="system-card__title"><?php echo T::e($item->title); ?></h3>
                            <p class="system-card__desc"><?php echo T::e(T::excerpt($item->introtext, 0)); ?></p>
                        </div>
                        <p class="system-card__action">
                            <a class="btn btn--sm" href="<?php echo T::e($url); ?>"<?php echo $newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                <?php echo Text::_('TPL_HOSPITAL_INTRANET_SYSTEM_OPEN'); ?>
                                <span class="sr-only"><?php echo T::e($item->title); ?><?php echo $newTab ? ' ' . Text::_('TPL_HOSPITAL_INTRANET_NEW_WINDOW') : ''; ?></span>
                                <?php echo T::icon($newTab ? 'external-link' : 'arrow-right'); ?>
                            </a>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>

    <div class="empty-state"<?php echo $visible ? ' hidden' : ''; ?> data-filter-empty>
        <?php echo T::icon('search'); ?>
        <p><?php echo Text::_($groups ? 'TPL_HOSPITAL_INTRANET_SYSTEMS_NONE_FOUND' : 'TPL_HOSPITAL_INTRANET_SYSTEMS_EMPTY'); ?></p>
    </div>
</div>
