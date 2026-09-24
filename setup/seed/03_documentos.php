<?php

/**
 * Biblioteca institucional: configuração de mídia, categorias, documentos de exemplo,
 * menus Protocolos/Documentos e módulo "Últimos protocolos adicionados" (idempotente).
 *
 *   docker compose cp setup/seed/03_documentos.php joomla:/tmp/seed.php
 *   docker compose exec -T -u www-data joomla php /tmp/seed.php
 */

const _JEXEC = 1;
\define('JPATH_BASE', '/var/www/html');
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Category;
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
$db      = $container->get('DatabaseDriver');
$adminId = (int) $db->setQuery(
	'SELECT u.id FROM #__users u JOIN #__user_usergroup_map m ON m.user_id = u.id WHERE m.group_id = 8 ORDER BY u.id LIMIT 1'
)->loadResult();
$app->loadIdentity(new User($adminId));

$json = static fn (array $data): string => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$log  = static function (string $message): void {
	echo $message, PHP_EOL;
};

// ---------------------------------------------------------------- 1. Mídia: tipos de arquivo aceitos (seção 27)

$log('Gerenciador de Mídia');
$docExt  = 'pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,txt,csv';
$imgExt  = 'bmp,gif,jpg,jpeg,png,webp,avif,svg';
$mimes   = implode(',', [
	'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/webp', 'image/avif', 'image/svg+xml',
	'application/pdf', 'text/plain', 'text/csv',
	'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet',
	'application/vnd.oasis.opendocument.presentation', 'application/zip',
]);
$media = json_decode((string) $db->setQuery("SELECT params FROM #__extensions WHERE element = 'com_media' AND type = 'component'")->loadResult(), true);
$media = array_merge($media, [
	'upload_maxsize'              => '20',
	'restrict_uploads'            => '1',
	'check_mime'                  => '1',
	'restrict_uploads_extensions' => $imgExt . ',' . $docExt,
	'image_extensions'            => $imgExt,
	'doc_extensions'              => $docExt,
	'audio_extensions'            => '',
	'video_extensions'            => '',
	'upload_mime'                 => $mimes,
	'upload_mime_illegal'         => 'text/html,application/x-php,application/x-httpd-php,application/javascript',
]);
$db->setQuery('UPDATE #__extensions SET params = ' . $db->quote($json($media)) . " WHERE element = 'com_media' AND type = 'component'")->execute();

// Adaptador local com as pastas "images" e "files"
$db->setQuery(
	'UPDATE #__extensions SET params = ' . $db->quote($json(['directories' => [
		'directories0' => ['directory' => 'images', 'thumbs' => 0],
		'directories1' => ['directory' => 'files', 'thumbs' => 0],
	]])) . " WHERE element = 'local' AND folder = 'filesystem'"
)->execute();

// Bloqueia execução de scripts na pasta de arquivos enviados
$filesDir = JPATH_ROOT . '/files/documentos';
is_dir($filesDir) || mkdir($filesDir, 0755, true);
file_put_contents(JPATH_ROOT . '/files/.htaccess', "# Intranet Hospitalar: arquivos enviados nunca são executados\n<FilesMatch \"\\.(php[0-9]?|phtml|phar|pl|py|cgi|sh|shtml)$\">\n  Require all denied\n</FilesMatch>\nOptions -ExecCGI -Indexes\nRemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8 .phar\nRemoveType .php .phtml .phar\n");

// ---------------------------------------------------------------- 2. Categorias (tipos da seção 11)

$log('Categorias');
$categories = [];
foreach (['Protocolos', 'POPs', 'Manuais', 'Formulários', 'Políticas', 'Normas', 'Fluxogramas', 'Treinamentos', 'Documentos de RH', 'Documentos de TI'] as $title) {
	$alias = OutputFilter::stringURLSafe($title);
	$id    = (int) $db->setQuery(
		"SELECT id FROM #__categories WHERE extension = 'com_documentos' AND alias = " . $db->quote($alias)
	)->loadResult();

	if (!$id) {
		$table = new Category($db);
		$table->setLocation(1, 'last-child');
		$table->bind([
			'title' => $title, 'alias' => $alias, 'extension' => 'com_documentos', 'published' => 1,
			'access' => 1, 'language' => '*', 'description' => '',
			'params' => $json(['category_layout' => '', 'image' => '']), 'metadata' => $json(['author' => '', 'robots' => '']),
		]);

		if (!$table->check() || !$table->store()) {
			throw new RuntimeException('Categoria "' . $title . '": ' . $table->getError());
		}

		$table->rebuildPath($table->id);
		$id = (int) $table->id;
		$log('  ' . $title);
	}

	$categories[$title] = $id;
}

// ---------------------------------------------------------------- 3. Documentos de exemplo

// PDF mínimo e válido (uma página) para demonstração
$pdf = static function (string $path, string $codigo, string $titulo, string $setor, string $versao): void {
	if (is_file($path)) {
		return;
	}

	$latin = static fn (string $text): string => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], mb_convert_encoding($text, 'Windows-1252', 'UTF-8'));
	$lines = [
		['F2', 11, 72, 770, 'HOSPITAL SANTA AURORA'],
		['F2', 20, 72, 730, $codigo],
		['F1', 15, 72, 704, $titulo],
		['F1', 11, 72, 670, 'Setor responsável: ' . $setor . '   |   Versão ' . $versao],
		['F1', 11, 72, 630, 'Documento fictício gerado para demonstração da Intranet Hospitalar.'],
		['F1', 11, 72, 612, 'Substitua pelo arquivo oficial no painel do Joomla (Componentes > Documentos).'],
	];
	$stream = "0.03 0.36 0.39 rg 0 800 612 42 re f\n";
	foreach ($lines as [$font, $size, $x, $y, $text]) {
		$color   = $y === 770 ? '1 1 1 rg' : '0.06 0.16 0.18 rg';
		$yy      = $y === 770 ? 814 : $y;
		$stream .= "BT $color /$font $size Tf $x $yy Td (" . $latin($text) . ") Tj ET\n";
	}

	$objects = [
		'<< /Type /Catalog /Pages 2 0 R >>',
		'<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
		'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
		'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
		'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
		'<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . 'endstream',
	];

	$out     = "%PDF-1.4\n";
	$offsets = [];
	foreach ($objects as $i => $object) {
		$offsets[] = strlen($out);
		$out      .= ($i + 1) . " 0 obj\n" . $object . "\nendobj\n";
	}
	$xref = strlen($out);
	$out .= 'xref' . "\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
	foreach ($offsets as $offset) {
		$out .= sprintf("%010d 00000 n \n", $offset);
	}
	$out .= 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";

	file_put_contents($path, $out);
};

$log('Documentos');
$docs = [
	// código, título, categoria, setor, versão, publicação, revisão, autor, status, público-alvo, descrição
	['POP-ENF-042', 'Administração segura de medicamentos', 'POPs', 'Enfermagem', '4.2', '2026-09-15', '2027-09-15', 'Coordenação de Enfermagem', 'vigente', 'Enfermagem', 'Padroniza as etapas de preparo, identificação, administração e registro de medicamentos, aplicando os "certos" da medicação.'],
	['PRT-CCIH-018', 'Precauções e isolamento hospitalar', 'Protocolos', 'Controle de Infecção', '3.0', '2026-09-08', '2027-03-08', 'CCIH', 'vigente', 'Todos os colaboradores assistenciais', 'Define os tipos de precaução (padrão, contato, gotículas e aerossóis), a sinalização dos leitos e o uso de EPIs.'],
	['POP-FAR-027', 'Armazenamento de medicamentos termolábeis', 'POPs', 'Farmácia', '2.1', '2026-08-20', '2026-08-31', 'Farmácia Hospitalar', 'vigente', 'Farmácia, Enfermagem', 'Orienta o controle de temperatura, o registro diário e a conduta em caso de desvio na cadeia de frio.'],
	['PRT-SEG-005', 'Identificação correta do paciente', 'Protocolos', 'Segurança do Paciente', '5.0', '2026-08-05', '2027-08-05', 'Núcleo de Segurança do Paciente', 'vigente', 'Todos os colaboradores', 'Estabelece o uso da pulseira de identificação com dois identificadores e a checagem antes de qualquer procedimento.'],
	['PRT-SEG-004', 'Prevenção de quedas (versão anterior)', 'Protocolos', 'Segurança do Paciente', '1.0', '2024-03-10', '2025-03-10', 'Núcleo de Segurança do Paciente', 'obsoleto', 'Enfermagem', 'Versão substituída. Mantida apenas para histórico.'],
	['MAN-RH-003', 'Manual de integração do colaborador', 'Manuais', 'Recursos Humanos', '2.0', '2026-07-01', '2027-07-01', 'Recursos Humanos', 'vigente', 'Novos colaboradores', 'Boas-vindas, missão e valores, benefícios, rotinas administrativas e canais de atendimento.'],
	['FOR-RH-011', 'Solicitação de férias', 'Formulários', 'Recursos Humanos', '1.2', '2026-06-12', '', 'Recursos Humanos', 'vigente', 'Todos os colaboradores', 'Formulário para solicitação e aprovação de férias pela liderança.'],
	['POL-TI-001', 'Política de segurança da informação', 'Políticas', 'Tecnologia da Informação', '1.3', '2026-05-20', '2027-05-20', 'Tecnologia da Informação', 'vigente', 'Todos os colaboradores', 'Regras de uso de senhas, e-mail, estações de trabalho e proteção de dados de pacientes.'],
	['NOR-ADM-004', 'Uso de uniforme e identificação funcional', 'Normas', 'Administração', '1.0', '2026-04-02', '2027-04-02', 'Diretoria Administrativa', 'vigente', 'Todos os colaboradores', 'Define o uso obrigatório do crachá e as regras de uniforme por área.'],
	['FLU-ENF-002', 'Atendimento ao código azul', 'Fluxogramas', 'Enfermagem', '2.0', '2026-03-18', '2026-12-18', 'Coordenação de Enfermagem', 'em_revisao', 'Enfermagem, Corpo Clínico', 'Fluxo de acionamento e atendimento à parada cardiorrespiratória nas unidades de internação.'],
	['TRE-CCIH-007', 'Higienização das mãos', 'Treinamentos', 'Controle de Infecção', '1.1', '2026-02-10', '2027-02-10', 'CCIH', 'vigente', 'Todos os colaboradores', 'Material do treinamento sobre os 5 momentos para a higiene das mãos.'],
	['DTI-001', 'Guia de acesso aos sistemas', 'Documentos de TI', 'Tecnologia da Informação', '1.0', '2026-01-15', '', 'Tecnologia da Informação', 'vigente', 'Todos os colaboradores', 'Como solicitar acesso, redefinir senha e abrir chamados de TI.'],
];

$now = Factory::getDate()->toSql();
foreach ($docs as [$codigo, $titulo, $cat, $setor, $versao, $pub, $rev, $autor, $status, $publico, $descricao]) {
	$exists = (int) $db->setQuery('SELECT id FROM #__hospital_documentos WHERE codigo = ' . $db->quote($codigo))->loadResult();

	if ($exists) {
		continue;
	}

	$file = strtolower($codigo) . '.pdf';
	$pdf($filesDir . '/' . $file, $codigo, $titulo, $setor, $versao);

	$row = (object) [
		'catid' => $categories[$cat], 'codigo' => $codigo, 'titulo' => $titulo, 'alias' => OutputFilter::stringURLSafe($codigo),
		'descricao' => $descricao, 'setor' => $setor, 'versao' => $versao,
		'data_publicacao' => $pub, 'data_revisao' => $rev ?: null,
		'arquivo' => 'files/documentos/' . $file, 'autor' => $autor, 'status' => $status, 'publico_alvo' => $publico,
		'state' => 1, 'ordering' => 0, 'created' => $now, 'created_by' => $adminId, 'modified' => $now, 'modified_by' => $adminId,
	];
	$db->insertObject('#__hospital_documentos', $row);
	$log('  ' . $codigo . ' — ' . $titulo);
}

// ---------------------------------------------------------------- 4. Menus Protocolos e Documentos → biblioteca

$log('Menus');
$componentId = (int) $db->setQuery("SELECT extension_id FROM #__extensions WHERE element = 'com_documentos' AND type = 'component'")->loadResult();
$menus       = [
	'protocolos' => ['catids' => [$categories['Protocolos'], $categories['POPs']], 'intro' => 'Protocolos e procedimentos operacionais padrão (POPs) vigentes.'],
	'documentos' => ['catids' => [], 'intro' => 'Manuais, formulários, políticas, normas, fluxogramas e materiais de treinamento.'],
];

foreach ($menus as $alias => $params) {
	$db->setQuery(
		'UPDATE #__menu SET type = ' . $db->quote('component')
		. ', link = ' . $db->quote('index.php?option=com_documentos&view=documentos')
		. ', component_id = ' . $componentId
		. ', params = ' . $db->quote($json($params))
		. " WHERE menutype = 'mainmenu' AND alias = " . $db->quote($alias) . ' AND client_id = 0'
	)->execute();
	$log('  /' . $alias);
}

// Páginas provisórias "em implantação" deixam de ser usadas
$db->setQuery(
	"UPDATE #__content c JOIN #__categories cat ON cat.id = c.catid AND cat.alias = 'paginas'
	 SET c.state = -2 WHERE c.alias IN ('protocolos', 'documentos')"
)->execute();

// ---------------------------------------------------------------- 5. Home: Biblioteca institucional (seção 10)

$log('Home');
$title = 'Biblioteca institucional | Últimos protocolos adicionados | /protocolos';
$exists = (int) $db->setQuery(
	"SELECT id FROM #__modules WHERE module = 'mod_hospital_protocols' AND client_id = 0"
)->loadResult();

if (!$exists) {
	$home  = (int) $db->setQuery("SELECT id FROM #__menu WHERE menutype = 'mainmenu' AND home = 1 AND client_id = 0")->loadResult();
	$table = new Module($db);
	$table->bind([
		'title' => $title, 'module' => 'mod_hospital_protocols', 'position' => 'protocols', 'showtitle' => 1,
		'published' => 1, 'access' => 1, 'language' => '*', 'client_id' => 0, 'ordering' => 1,
		'note' => 'Título: "Rótulo | Título | link do Ver todas"',
		'params' => $json(['catids' => [$categories['Protocolos'], $categories['POPs']], 'count' => 4, 'layout' => '_:default']),
	]);
	$table->store();
	$db->setQuery('INSERT INTO #__modules_menu (moduleid, menuid) VALUES (' . (int) $table->id . ', ' . $home . ')')->execute();
	$log('  módulo: ' . $title);
}

$log('Concluído.');
