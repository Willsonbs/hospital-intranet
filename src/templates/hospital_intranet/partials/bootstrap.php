<?php

/**
 * Preparação comum a index.php, error.php e designsystem.php:
 * carrega os assets e monta as variáveis usadas pelos partials.
 *
 * @var Joomla\CMS\Document\HtmlDocument $this
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$app      = Factory::getApplication();
$wa       = $this->getWebAssetManager();
$params   = $this->params;
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$mediaUri = Uri::root(true) . '/media/templates/site/hospital_intranet';

$wa->usePreset('template.hospital_intranet')
    // Marca <html> como "com JS" antes da renderização: o CSS só esconde
    // painéis (busca, menu mobile) quando o JS existe para reabri-los.
    ->addInlineScript("document.documentElement.classList.add('js')", ['position' => 'before'], [], ['template.hospital_intranet.navigation']);

// Fontes locais: pré-carrega a variante latina (a mais usada)
$this->getPreloadManager()->preload(
    $mediaUri . '/fonts/figtree-latin.woff2',
    ['as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous']
);

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$this->setMetaData('theme-color', '#075B63');
$this->addHeadLink($mediaUri . '/images/favicon.svg', 'icon', 'rel', ['type' => 'image/svg+xml']);

// Valor de um campo "media" do Joomla -> URL limpa (remove o sufixo #joomlaImage://)
$tplImageUrl = static function (?string $value): string {
    if (!$value) {
        return '';
    }

    return Uri::root(true) . '/' . ltrim(HTMLHelper::_('cleanImageURL', $value)->url, '/');
};

// Marca
$brandName     = htmlspecialchars($params->get('brandName', 'Hospital'), ENT_QUOTES, 'UTF-8');
$brandSubtitle = htmlspecialchars($params->get('brandSubtitle', 'Santa Aurora'), ENT_QUOTES, 'UTF-8');
$logoUrl       = $tplImageUrl($params->get('logoFile'));

// Página inicial?
$menu   = $app->getMenu();
$active = $menu->getActive();
$isHome = $active !== null && $active->id === $menu->getDefault($this->language)->id
    && $app->getInput()->getCmd('option') === 'com_content'
    && $app->getInput()->getCmd('view') === 'featured';

// Imagem do topo (hero); sem imagem cadastrada usa o fundo padrão do CSS
$heroImage = $tplImageUrl($params->get('heroImage'));
$heroStyle = $heroImage ? ' style="--hero-image: url(\'' . htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') . '\')"' : '';

// Saudação pelo horário do servidor (fuso da configuração global).
// greeting.js atualiza pelo relógio do navegador se a página vier do cache.
$hour     = (int) Factory::getDate('now', $app->get('offset', 'UTC'))->format('G', true);
$greeting = Text::_(match (true) {
    $hour >= 5 && $hour < 12  => 'TPL_HOSPITAL_INTRANET_GREETING_MORNING',
    $hour >= 12 && $hour < 18 => 'TPL_HOSPITAL_INTRANET_GREETING_AFTERNOON',
    default                   => 'TPL_HOSPITAL_INTRANET_GREETING_EVENING',
});

// Título para o <h1> da faixa: o <title> sem o nome do site
// (a configuração global pode acrescentá-lo antes ou depois).
$pageTitle = $this->getTitle();
$siteRaw   = $app->get('sitename');

foreach ([Text::sprintf('JPAGETITLE', '%TITLE%', $siteRaw), Text::sprintf('JPAGETITLE', $siteRaw, '%TITLE%')] as $pattern) {
    [$before, $after] = explode('%TITLE%', $pattern) + [1 => ''];

    if ($pageTitle !== $siteRaw && str_starts_with($pageTitle, $before) && str_ends_with($pageTitle, $after)) {
        $pageTitle = substr($pageTitle, strlen($before), strlen($pageTitle) - strlen($before) - strlen($after));
        break;
    }
}
