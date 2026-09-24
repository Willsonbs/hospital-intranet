<?php

/**
 * Chrome "section": seção da home com rótulo, título e link opcional.
 *
 * O título do módulo pode trazer o rótulo antes de uma barra vertical:
 *   "Acesso rápido | Sistemas e ferramentas"
 *   → rótulo "ACESSO RÁPIDO" + título "Sistemas e ferramentas"
 *
 * O layout do módulo pode definir $module->sectionLink = ['url' => ..., 'text' => ...]
 * para exibir o link "Ver todas ›" ao lado do título.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$module = $displayData['module'];
$params = $displayData['params'];

if ((string) $module->content === '') {
    return;
}

$eyebrow = '';
$title   = $module->title;

if (str_contains($title, '|')) {
    [$eyebrow, $title] = array_map('trim', explode('|', $title, 2));
}

$headingTag = htmlspecialchars($params->get('header_tag', 'h2'), ENT_QUOTES, 'UTF-8');
$headingId  = 'mod-title-' . $module->id;
$moduleTag  = $module->showtitle ? 'section' : 'div';
$class      = trim('module-section ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'));
$link       = $module->sectionLink ?? null;
?>
<<?php echo $moduleTag; ?> class="<?php echo $class; ?>"<?php echo $module->showtitle ? ' aria-labelledby="' . $headingId . '"' : ''; ?>>
    <?php if ($module->showtitle) : ?>
        <div class="section-head">
            <div>
                <?php if ($eyebrow !== '') : ?>
                    <p class="eyebrow"><?php echo htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <<?php echo $headingTag; ?> class="section-head__title" id="<?php echo $headingId; ?>"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></<?php echo $headingTag; ?>>
            </div>
            <?php if ($link) : ?>
                <a class="link-arrow" href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($link['text'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php echo LayoutHelper::render('hospital.icon', ['name' => 'chevron-right']); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php echo $module->content; ?>
</<?php echo $moduleTag; ?>>
