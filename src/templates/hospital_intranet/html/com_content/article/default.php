<?php

/**
 * Página do artigo: escolhe o layout pela seção da intranet.
 *   biblioteca → default_documento.php (ficha do documento + download)
 *   noticias   → default_noticia.php
 *   sistemas   → default_sistema.php   (atalho para o sistema)
 *   avisos     → default_aviso.php
 * Outras categorias usam o layout padrão do Joomla.
 *
 * O título fica na faixa do topo (<h1> do template); os layouts não o repetem.
 *
 * @var \Joomla\Component\Content\Site\View\Article\HtmlView $this
 */

defined('_JEXEC') or die;

require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';

$layout = [
    'biblioteca' => 'documento',
    'noticias'   => 'noticia',
    'sistemas'   => 'sistema',
    'avisos'     => 'aviso',
][HospitalIntranetTemplate::section((int) $this->item->catid)] ?? null;

if ($layout) {
    echo $this->loadTemplate($layout);

    return;
}

require JPATH_SITE . '/components/com_content/tmpl/article/default.php';
