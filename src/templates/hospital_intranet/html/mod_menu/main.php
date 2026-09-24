<?php

/**
 * Menu principal do header.
 *
 * - Itens de 1º nível na horizontal (desktop) ou em lista (mobile).
 * - Item com filhos: o link continua navegável e um botão ao lado
 *   abre o submenu (padrão "disclosure", acessível por teclado).
 * - Item ativo recebe aria-current e o indicador inferior.
 *
 * Variáveis do mod_menu: $list, $path, $active_id, $params, $module.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Filter\OutputFilter;

$startLevel = (int) $params->get('startLevel', 1);
?>
<ul class="nav-menu" data-nav-menu>
<?php foreach ($list as $item) :
    $itemParams = $item->getParams();
    $aliasTo    = $item->type === 'alias' ? (int) $itemParams->get('aliasoptions') : 0;
    $isCurrent  = $item->id == $active_id || ($aliasTo && $aliasTo == $active_id);
    $isActive   = in_array($item->id, $path) || ($aliasTo && in_array($aliasTo, $path));
    $isTop      = (int) $item->level === $startLevel;

    $classes = ['nav-menu__item', $isTop ? 'nav-menu__item--top' : 'nav-menu__item--sub'];

    if ($isActive) {
        $classes[] = 'is-active';
    }

    if ($item->deeper) {
        $classes[] = 'has-children';
    }

    echo '<li class="' . implode(' ', $classes) . '">';

    if (in_array($item->type, ['separator', 'heading'], true)) {
        echo '<span class="nav-menu__link">' . $item->title . '</span>';
    } else {
        $attrs = '';

        if ($isCurrent) {
            $attrs .= ' aria-current="page"';
        }

        if ($item->browserNav == 1) {
            $attrs .= ' target="_blank" rel="noopener noreferrer"';
        }

        $href = OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false));

        echo '<a class="nav-menu__link" href="' . $href . '"' . $attrs . '>';

        // "Classe do ícone do link" no item de menu = nome de um ícone Lucide
        if (!$isTop && $item->menu_icon) {
            echo LayoutHelper::render('hospital.icon', ['name' => $item->menu_icon]);
        }

        echo '<span>' . $item->title . '</span>';

        if ($item->browserNav == 1) {
            echo '<span class="sr-only"> ' . Text::_('TPL_HOSPITAL_INTRANET_NEW_WINDOW') . '</span>';
        }

        echo '</a>';
    }

    if ($item->deeper) {
        $subId = 'submenu-' . $module->id . '-' . $item->id;

        if ($isTop) {
            echo '<button type="button" class="nav-menu__toggle" aria-expanded="false" aria-controls="' . $subId . '">'
                . LayoutHelper::render('hospital.icon', ['name' => 'chevron-down'])
                . '<span class="sr-only">' . Text::sprintf('TPL_HOSPITAL_INTRANET_SUBMENU_TOGGLE', $item->title) . '</span>'
                . '</button>';
        }

        echo '<ul class="nav-menu__sub" id="' . $subId . '">';
    } elseif ($item->shallower) {
        echo '</li>' . str_repeat('</ul></li>', $item->level_diff);
    } else {
        echo '</li>';
    }
endforeach; ?>
</ul>
