<?php

/**
 * Conteúdo inicial da Intranet Hospitalar (idempotente: pode ser executado mais de uma vez).
 *
 *   docker compose cp setup/seed/02_conteudo.php joomla:/tmp/seed.php
 *   docker compose exec -T -u www-data joomla php /tmp/seed.php
 *
 * Cria: categorias de notícias, notícias de exemplo (com imagens), páginas do menu,
 * menu principal na ordem da especificação (seção 4), módulos da home e do rodapé.
 */

const _JEXEC = 1;
\define('JPATH_BASE', '/var/www/html');
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Category;
use Joomla\CMS\Table\Content;
use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\Module;
use Joomla\CMS\User\User;

$container = Factory::getContainer();
$container->alias('session', 'session.cli')
	->alias('JSession', 'session.cli')
	->alias(\Joomla\CMS\Session\Session::class, 'session.cli')
	->alias(\Joomla\Session\Session::class, 'session.cli')
	->alias(\Joomla\Session\SessionInterface::class, 'session.cli');

$app = $container->get(\Joomla\Console\Application::class);
Factory::$application = $app;

/** @var \Joomla\Database\DatabaseDriver $db */
$db = $container->get('DatabaseDriver');

$adminId = (int) $db->setQuery(
	'SELECT u.id FROM #__users u JOIN #__user_usergroup_map m ON m.user_id = u.id WHERE m.group_id = 8 ORDER BY u.id LIMIT 1'
)->loadResult();
$app->loadIdentity(new User($adminId));

$json = static fn (array $data): string => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$log  = static function (string $message): void {
	echo $message, PHP_EOL;
};

$extensionId = static function (string $element) use ($db): int {
	return (int) $db->setQuery(
		$db->getQuery(true)->select('extension_id')->from('#__extensions')
			->where('element = ' . $db->quote($element))->where('type = ' . $db->quote('component'))
	)->loadResult();
};

// ---------------------------------------------------------------- helpers

$category = static function (string $title, int $parentId, string $description = '') use ($db, $json, $log): int {
	$alias = OutputFilter::stringURLSafe($title);
	$id    = (int) $db->setQuery(
		$db->getQuery(true)->select('id')->from('#__categories')
			->where('extension = ' . $db->quote('com_content'))
			->where('alias = ' . $db->quote($alias))
			->where('parent_id = ' . $parentId)
	)->loadResult();

	if ($id) {
		return $id;
	}

	$table = new Category($db);
	$table->setLocation($parentId, 'last-child');
	$table->bind([
		'title'       => $title,
		'alias'       => $alias,
		'extension'   => 'com_content',
		'description' => $description,
		'published'   => 1,
		'access'      => 1,
		'language'    => '*',
		'params'      => $json(['category_layout' => '', 'image' => '']),
		'metadata'    => $json(['author' => '', 'robots' => '']),
	]);

	if (!$table->check() || !$table->store()) {
		throw new RuntimeException('Categoria "' . $title . '": ' . $table->getError());
	}

	$table->rebuildPath($table->id);
	$log("  categoria: $title");

	return (int) $table->id;
};

$article = static function (array $data) use ($db, $json, $log): int {
	$alias = OutputFilter::stringURLSafe($data['title']);
	$id    = (int) $db->setQuery(
		$db->getQuery(true)->select('id')->from('#__content')
			->where('alias = ' . $db->quote($alias))->where('catid = ' . (int) $data['catid'])
	)->loadResult();

	if ($id) {
		return $id;
	}

	$table = new Content($db);
	$table->bind([
		'title'      => $data['title'],
		'alias'      => $alias,
		'introtext'  => $data['introtext'],
		'fulltext'   => $data['fulltext'] ?? '',
		'catid'      => $data['catid'],
		'state'      => 1,
		'featured'   => (int) ($data['featured'] ?? 0),
		'access'     => 1,
		'language'   => '*',
		'created'    => $data['date'],
		'publish_up' => $data['date'],
		'images'     => $json([
			'image_intro'     => $data['image'] ?? '',
			'image_intro_alt' => $data['image_alt'] ?? '',
			'image_fulltext'  => $data['image'] ?? '',
			'image_fulltext_alt' => $data['image_alt'] ?? '',
		]),
		'urls'       => '{}',
		'attribs'    => '{}',
		'metadata'   => $json(['robots' => '', 'author' => '', 'rights' => '']),
		'metakey'    => '',
		'metadesc'   => '',
	]);

	if (!$table->check() || !$table->store()) {
		throw new RuntimeException('Artigo "' . $data['title'] . '": ' . $table->getError());
	}

	$id = (int) $table->id;

	// Fluxo de trabalho padrão do Joomla (estágio "Basic")
	$db->setQuery(
		'INSERT IGNORE INTO #__workflow_associations (item_id, stage_id, extension) VALUES (' . $id . ', 1, ' . $db->quote('com_content.article') . ')'
	)->execute();

	if (!empty($data['featured'])) {
		$db->setQuery('INSERT IGNORE INTO #__content_frontpage (content_id, ordering) VALUES (' . $id . ', 0)')->execute();
	}

	$log('  artigo: ' . $data['title']);

	return $id;
};

$menuItem = static function (array $data) use ($db, $json, $log): int {
	$parent = (int) ($data['parent_id'] ?? 1);
	$alias  = $data['alias'] ?? OutputFilter::stringURLSafe($data['title']);
	$id     = (int) $db->setQuery(
		$db->getQuery(true)->select('id')->from('#__menu')
			->where('menutype = ' . $db->quote($data['menutype']))
			->where('alias = ' . $db->quote($alias))
			->where('parent_id = ' . $parent)
			->where('client_id = 0')
	)->loadResult();

	if ($id) {
		return $id;
	}

	$table = new Menu($db);
	$table->setLocation($parent, 'last-child');
	$table->bind([
		'menutype'          => $data['menutype'],
		'title'             => $data['title'],
		'alias'             => $alias,
		'link'              => $data['link'],
		'type'              => $data['type'] ?? 'component',
		'component_id'      => $data['component_id'] ?? 0,
		'published'         => 1,
		'access'            => 1,
		'language'          => '*',
		'browserNav'        => 0,
		'img'               => '',
		'template_style_id' => 0,
		'home'              => 0,
		'client_id'         => 0,
		'params'            => $json($data['params'] ?? []),
	]);

	if (!$table->check() || !$table->store()) {
		throw new RuntimeException('Menu "' . $data['title'] . '": ' . $table->getError());
	}

	$table->rebuildPath($table->id);
	$log('  menu: ' . $data['title']);

	return (int) $table->id;
};

$module = static function (array $data, array $menuIds = [0]) use ($db, $json, $log): int {
	$id = (int) $db->setQuery(
		$db->getQuery(true)->select('id')->from('#__modules')
			->where('module = ' . $db->quote($data['module']))
			->where('title = ' . $db->quote($data['title']))
			->where('client_id = 0')
	)->loadResult();

	if ($id) {
		return $id;
	}

	$table = new Module($db);
	$table->bind([
		'title'     => $data['title'],
		'module'    => $data['module'],
		'position'  => $data['position'],
		'content'   => $data['content'] ?? '',
		'showtitle' => (int) ($data['showtitle'] ?? 1),
		'published' => 1,
		'access'    => 1,
		'language'  => '*',
		'client_id' => 0,
		'ordering'  => (int) ($data['ordering'] ?? 1),
		'note'      => $data['note'] ?? '',
		'params'    => $json($data['params'] ?? []),
	]);

	if (!$table->check() || !$table->store()) {
		throw new RuntimeException('Módulo "' . $data['title'] . '": ' . $table->getError());
	}

	foreach ($menuIds as $menuId) {
		$db->setQuery('INSERT IGNORE INTO #__modules_menu (moduleid, menuid) VALUES (' . (int) $table->id . ', ' . (int) $menuId . ')')->execute();
	}

	$log('  módulo: ' . $data['title']);

	return (int) $table->id;
};

// Ilustração de capa gerada localmente (substitua por fotos reais pelo Gerenciador de Mídia)
$illustration = static function (string $file, int $glyph, array $from, array $to): string {
	$dir  = JPATH_ROOT . '/images/noticias';
	$path = $dir . '/' . $file;

	if (is_file($path)) {
		return 'images/noticias/' . $file;
	}

	is_dir($dir) || mkdir($dir, 0755, true);

	[$w, $h] = [1200, 750];
	$img = imagecreatetruecolor($w, $h);
	imagealphablending($img, true);

	for ($y = 0; $y < $h; $y++) {
		$t = $y / $h;
		imageline($img, 0, $y, $w, $y, imagecolorallocate(
			$img,
			(int) ($from[0] + ($to[0] - $from[0]) * $t),
			(int) ($from[1] + ($to[1] - $from[1]) * $t),
			(int) ($from[2] + ($to[2] - $from[2]) * $t)
		));
	}

	$soft = imagecolorallocatealpha($img, 255, 255, 255, 112);
	imagefilledellipse($img, 980, 120, 520, 520, $soft);
	imagefilledellipse($img, 160, 700, 440, 440, $soft);
	imagefilledellipse($img, 1100, 690, 260, 260, $soft);

	$font  = JPATH_ROOT . '/media/vendor/fontawesome-free/webfonts/fa-solid-900.ttf';
	$char  = mb_chr($glyph, 'UTF-8');
	$size  = 230;
	$box   = imagettfbbox($size, 0, $font, $char);
	$x     = (int) (($w - ($box[2] - $box[0])) / 2 - $box[0]);
	$y     = (int) (($h - ($box[1] - $box[7])) / 2 - $box[7]);
	imagettftext($img, $size, 0, $x, $y, imagecolorallocatealpha($img, 255, 255, 255, 20), $font, $char);

	imagewebp($img, $path, 82);
	imagedestroy($img);

	return 'images/noticias/' . $file;
};

// ---------------------------------------------------------------- 1. Categorias (seção 9)

$log('Categorias');
$noticias = $category('Notícias', 1, '<p>Notícias e comunicados institucionais do hospital.</p>');
$cats     = [];

foreach (['Institucional', 'Pessoas', 'Saúde e Bem-estar', 'Tecnologia', 'Recursos Humanos', 'Eventos', 'Comunicados', 'TI'] as $title) {
	$cats[$title] = $category($title, $noticias);
}

$paginas = $category('Páginas', 1, '<p>Páginas institucionais usadas pelo menu.</p>');

// ---------------------------------------------------------------- 2. Notícias de exemplo (seção 31)

$log('Notícias');
$article([
	'title'     => 'Hospital recebe certificação de qualidade nacional',
	'catid'     => $cats['Institucional'],
	'featured'  => 1,
	'date'      => '2026-09-18 09:00:00',
	'image'     => $illustration('certificacao-qualidade.webp', 0xf559, [4, 54, 60], [11, 115, 120]),
	'image_alt' => 'Ilustração de medalha representando a certificação de qualidade',
	'introtext' => '<p>Após meses de preparação, o Hospital Santa Aurora conquistou a certificação nacional de qualidade, que reconhece a segurança do paciente e a excelência dos processos assistenciais.</p>',
	'fulltext'  => '<p>A avaliação envolveu todas as áreas do hospital, da recepção ao centro cirúrgico. Os avaliadores destacaram a adesão aos protocolos institucionais, a cultura de notificação de eventos e o engajamento das equipes multiprofissionais.</p><p>A diretoria agradece a cada colaborador que contribuiu para essa conquista. A certificação tem validade de três anos e será acompanhada por visitas periódicas de manutenção.</p>',
]);
$article([
	'title'     => 'Semana da Enfermagem celebra quem transforma o cuidado',
	'catid'     => $cats['Pessoas'],
	'featured'  => 1,
	'date'      => '2026-09-10 10:30:00',
	'image'     => $illustration('semana-enfermagem.webp', 0xf82f, [6, 72, 80], [46, 155, 118]),
	'image_alt' => 'Ilustração de profissional de enfermagem',
	'introtext' => '<p>Palestras, oficinas e homenagens marcam a programação da Semana da Enfermagem, que reconhece o papel essencial dos profissionais no cuidado aos pacientes.</p>',
	'fulltext'  => '<p>A programação acontece no Auditório Principal e inclui rodas de conversa sobre segurança do paciente, oficinas práticas e a entrega do prêmio de reconhecimento às equipes.</p><p>As inscrições para as oficinas podem ser feitas com a Educação Permanente, pelo ramal 2095.</p>',
]);
$article([
	'title'     => 'Nova campanha incentiva hábitos saudáveis no trabalho',
	'catid'     => $cats['Saúde e Bem-estar'],
	'featured'  => 1,
	'date'      => '2026-09-02 08:00:00',
	'image'     => $illustration('habitos-saudaveis.webp', 0xf5d1, [11, 115, 120], [127, 209, 192]),
	'image_alt' => 'Ilustração de maçã representando alimentação saudável',
	'introtext' => '<p>A campanha "Cuidar de quem cuida" traz dicas de alimentação, pausas ativas e ginástica laboral para melhorar a qualidade de vida dos colaboradores.</p>',
	'fulltext'  => '<p>Durante todo o mês, o refeitório terá opções especiais preparadas pela Nutrição e Dietética, e as equipes poderão agendar sessões de ginástica laboral diretamente com o setor de Recursos Humanos.</p>',
]);

// ---------------------------------------------------------------- 3. Páginas do menu

$log('Páginas');
$sistemasArticle = $article([
	'title'     => 'Sistemas e ferramentas',
	'catid'     => $paginas,
	'date'      => '2026-09-01 08:00:00',
	'introtext' => '<p>Acesse os sistemas do hospital organizados por área. Os links são mantidos pela equipe de TI.</p><p>{loadposition sistemas-catalogo}</p>',
]);

$emImplantacao = static fn (string $what): string => '<p>Esta área está em implantação.</p><p>Em breve você encontrará aqui ' . $what . '</p>';

$eventosArticle = $article([
	'title'     => 'Eventos',
	'catid'     => $paginas,
	'date'      => '2026-09-01 08:00:00',
	'introtext' => $emImplantacao('o calendário de eventos, treinamentos e reuniões institucionais.'),
]);
$protocolosArticle = $article([
	'title'     => 'Protocolos',
	'catid'     => $paginas,
	'date'      => '2026-09-01 08:00:00',
	'introtext' => $emImplantacao('os protocolos e POPs institucionais vigentes.'),
]);
$documentosArticle = $article([
	'title'     => 'Documentos',
	'catid'     => $paginas,
	'date'      => '2026-09-01 08:00:00',
	'introtext' => $emImplantacao('a biblioteca de manuais, formulários, políticas e normas.'),
]);

// ---------------------------------------------------------------- 4. Menu principal (seções 4 e 13.8)

$log('Menu principal');
$contentId  = $extensionId('com_content');
$pageParams = [
	'show_title' => 0, 'show_category' => 0, 'show_author' => 0, 'show_create_date' => 0,
	'show_publish_date' => 0, 'show_modify_date' => 0, 'show_hits' => 0, 'show_item_navigation' => 0,
	'show_tags' => 0, 'info_block_position' => 0, 'show_readmore' => 0,
];
$articleLink = static fn (int $id): string => 'index.php?option=com_content&view=article&id=' . $id;

$home = (int) $db->setQuery("SELECT id FROM #__menu WHERE menutype = 'mainmenu' AND home = 1 AND client_id = 0")->loadResult();
$db->setQuery('UPDATE #__menu SET title = ' . $db->quote('Início') . ' WHERE id = ' . $home)->execute();

$sistemas = $menuItem([
	'menutype' => 'mainmenu', 'title' => 'Sistemas', 'alias' => 'sistemas',
	'link' => $articleLink($sistemasArticle), 'component_id' => $contentId,
	'params' => $pageParams + ['pageclass_sfx' => ' hi-plain'],
]);

foreach (['Assistenciais', 'Administrativos', 'Recursos Humanos', 'Financeiro', 'TI', 'BI e Indicadores'] as $area) {
	$menuItem([
		'menutype' => 'mainmenu', 'title' => $area, 'parent_id' => $sistemas,
		'alias' => 'sistemas-' . OutputFilter::stringURLSafe($area),
		'type' => 'url', 'link' => '/sistemas#' . OutputFilter::stringURLSafe($area),
		'params' => ['menu-anchor_title' => 'Sistemas ' . $area, 'menu_show' => 1],
	]);
}

$ramais = (int) $db->setQuery("SELECT id FROM #__menu WHERE menutype = 'mainmenu' AND alias = 'ramais' AND client_id = 0")->loadResult();

$eventos = $menuItem([
	'menutype' => 'mainmenu', 'title' => 'Eventos', 'alias' => 'eventos',
	'link' => $articleLink($eventosArticle), 'component_id' => $contentId, 'params' => $pageParams,
]);
$protocolos = $menuItem([
	'menutype' => 'mainmenu', 'title' => 'Protocolos', 'alias' => 'protocolos',
	'link' => $articleLink($protocolosArticle), 'component_id' => $contentId, 'params' => $pageParams,
]);
$noticiasMenu = $menuItem([
	'menutype' => 'mainmenu', 'title' => 'Notícias', 'alias' => 'noticias',
	'link' => 'index.php?option=com_content&view=category&layout=blog&id=' . $noticias,
	'component_id' => $contentId,
	'params' => [
		'show_category_title' => 0, 'show_description' => 0, 'show_subcategory_content' => -1,
		'num_leading_articles' => 0, 'num_intro_articles' => 9, 'num_links' => 0, 'num_columns' => 1,
		'orderby_pri' => 'none', 'orderby_sec' => 'rdate', 'order_date' => 'published',
		'show_pagination' => 2, 'show_pagination_results' => 0, 'show_featured' => 'show',
		// exibição da notícia aberta a partir desta página
		'show_title' => 1, 'show_category' => 1, 'link_category' => 1, 'show_author' => 0,
		'show_create_date' => 0, 'show_publish_date' => 1, 'show_modify_date' => 0, 'show_hits' => 0,
		'show_item_navigation' => 0, 'show_tags' => 1, 'info_block_position' => 0,
		'show_readmore' => 1, 'show_intro' => 1,
	],
]);
$documentos = $menuItem([
	'menutype' => 'mainmenu', 'title' => 'Documentos', 'alias' => 'documentos',
	'link' => $articleLink($documentosArticle), 'component_id' => $contentId, 'params' => $pageParams,
]);

// Ordem da especificação: Início, Sistemas, Ramais, Eventos, Protocolos, Notícias, Documentos
$previous = $home;
foreach ([$sistemas, $ramais, $eventos, $protocolos, $noticiasMenu, $documentos] as $id) {
	(new Menu($db))->moveByReference($previous, 'after', $id);
	$previous = $id;
}

// ---------------------------------------------------------------- 5. Rodapé (seção 19)

$log('Rodapé');
$db->setQuery(
	"INSERT IGNORE INTO #__menu_types (asset_id, menutype, title, description, client_id)
	 VALUES (0, 'footer-links', 'Links úteis', 'Links do rodapé', 0)"
)->execute();

foreach (['Sistemas' => $sistemas, 'Ramais' => $ramais, 'Documentos' => $documentos, 'Eventos' => $eventos] as $title => $target) {
	$menuItem([
		'menutype' => 'footer-links', 'title' => $title, 'alias' => 'rodape-' . OutputFilter::stringURLSafe($title),
		'type' => 'alias', 'link' => 'index.php?Itemid=', 'params' => ['aliasoptions' => $target, 'alias_redirect' => 0],
	]);
}

$module([
	'title' => 'Links úteis', 'module' => 'mod_menu', 'position' => 'footer-menu', 'showtitle' => 1,
	'params' => ['menutype' => 'footer-links', 'startLevel' => 1, 'endLevel' => 0, 'showAllChildren' => 0, 'layout' => '_:default'],
]);
$module([
	'title' => 'TI Hospitalar', 'module' => 'mod_custom', 'position' => 'footer', 'showtitle' => 1,
	'content' => '<p>Suporte: ramal 2000</p><p><a href="/ramais">Diretório de ramais</a></p>',
	'params' => ['prepare_content' => 0, 'layout' => '_:default'],
]);

// ---------------------------------------------------------------- 6. Home: Últimas notícias (seção 8)

$log('Home');
$module([
	'title'    => 'Fique por dentro | Últimas notícias | /noticias',
	'module'   => 'mod_articles',
	'position' => 'news',
	'note'     => 'Título: "Rótulo | Título | link do Ver todas"',
	'params'   => [
		'mode' => 'normal', 'count' => 3, 'category_filtering_type' => 1, 'catid' => [$noticias],
		'show_child_category_articles' => 1, 'levels' => 9, 'show_featured' => 'show',
		'article_ordering' => 'a.publish_up', 'article_ordering_direction' => 'DESC',
		'introtext_limit' => 140, 'show_on_article_page' => 1, 'exclude_current' => 1,
		'layout' => 'hospital_intranet:cards', 'owncache' => 1, 'cache_time' => 900,
	],
], [$home]);

// ---------------------------------------------------------------- 7. Página Sistemas: catálogo (seção 14)

$log('Catálogo de sistemas');
$systems = [
	['Sistema Assistencial', 'Prontuário e segurança de pacientes', 'https://assistencial.exemplo.local', 'fa-solid fa-notes-medical', 'green', 'Assistenciais', 1],
	['Agenda de Salas', 'Reservas de espaços e equipamentos', 'https://agenda.exemplo.local', 'fa-solid fa-calendar-days', 'orange', 'Administrativos', 1],
	['Documentos', 'Protocolos, manuais e formulários', '/documentos', 'fa-solid fa-file-lines', 'red', 'Administrativos', 0],
	['Portal do Colaborador', 'Holerites, benefícios e dados cadastrais', 'https://portal.exemplo.local', 'fa-solid fa-user-group', 'blue', 'Recursos Humanos', 1],
	['ERP Financeiro', 'Contas a pagar, compras e faturamento', 'https://erp.exemplo.local', 'fa-solid fa-wallet', 'slate', 'Financeiro', 1],
	['Chamados de TI', 'Suporte técnico e acompanhamento', 'https://chamados.exemplo.local', 'fa-solid fa-desktop', 'slate', 'TI', 1],
	['Indicadores', 'Painéis e resultados institucionais', 'https://indicadores.exemplo.local', 'fa-solid fa-chart-column', 'purple', 'BI e Indicadores', 1],
];
$items = [];
foreach ($systems as $i => [$title, $desc, $url, $icon, $tone, $cat, $newTab]) {
	$items['items' . $i] = [
		'published' => '1', 'title' => $title, 'description' => $desc, 'url' => $url, 'icon' => $icon,
		'tone' => $tone, 'category' => $cat, 'new_tab' => (string) $newTab, 'access' => '1',
	];
}
$module([
	'title'     => 'Catálogo de sistemas',
	'module'    => 'mod_hospital_quick_access',
	'position'  => 'sistemas-catalogo',
	'showtitle' => 0,
	'note'      => 'Exibido na página Sistemas via {loadposition sistemas-catalogo}',
	'params'    => [
		'items' => $items, 'category_filter' => '', 'layout' => '_:catalog',
		'category_order' => "Assistenciais\nAdministrativos\nRecursos Humanos\nFinanceiro\nTI\nBI e Indicadores",
	],
]);

$log('Concluído.');
