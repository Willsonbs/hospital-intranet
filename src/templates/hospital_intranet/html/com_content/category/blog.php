<?php

/**
 * Páginas de categoria (blog): escolhe o layout pela seção da intranet.
 *   sistemas   → blog_sistemas.php   (cards agrupados por categoria)
 *   noticias   → blog_noticias.php   (cards com filtro por categoria e paginação)
 *   biblioteca → blog_biblioteca.php (documentos com busca e filtros)
 *   avisos     → blog_avisos.php
 * Outras categorias usam o layout padrão do Joomla.
 *
 * @var \Joomla\Component\Content\Site\View\Category\HtmlView $this
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

$layout = [
    'sistemas'   => 'sistemas',
    'noticias'   => 'noticias',
    'biblioteca' => 'biblioteca',
    'avisos'     => 'avisos',
][HospitalIntranetTemplate::section((int) $this->category->id)] ?? null;

if ($layout) {
    echo $this->loadTemplate($layout);

    return;
}

require JPATH_SITE . '/components/com_content/tmpl/category/blog.php';
