<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Table;

\defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class DocumentoTable extends Table
{
	/** Extensões aceitas no campo Arquivo (a validação de upload fica com o Gerenciador de Mídia) */
	private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'csv'];

	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__hospital_documentos', 'id', $db);
		$this->setColumnAlias('published', 'state');
		$this->setColumnAlias('title', 'titulo');
	}

	public function check()
	{
		$this->codigo = strtoupper(trim((string) $this->codigo));
		$this->titulo = trim((string) $this->titulo);
		$this->alias  = OutputFilter::stringURLSafe($this->codigo);

		if ($this->codigo === '' || $this->titulo === '' || !(int) $this->catid) {
			$this->setError(Text::_('COM_DOCUMENTOS_ERROR_REQUIRED'));

			return false;
		}

		if (!preg_match('/^[A-Z0-9][A-Z0-9._\-]*$/', $this->codigo)) {
			$this->setError(Text::_('COM_DOCUMENTOS_ERROR_CODIGO_FORMAT'));

			return false;
		}

		$this->versao = trim((string) $this->versao);

		if (!preg_match('/^\d+(\.\d+)*$/', $this->versao)) {
			$this->setError(Text::_('COM_DOCUMENTOS_ERROR_VERSAO_FORMAT'));

			return false;
		}

		if (!in_array($this->status, DocumentosHelper::STATUS, true)) {
			$this->status = 'vigente';
		}

		foreach (['data_publicacao', 'data_revisao'] as $field) {
			$value        = substr(trim((string) $this->$field), 0, 10);
			$this->$field = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
		}

		if ($this->data_publicacao && $this->data_revisao && $this->data_revisao < $this->data_publicacao) {
			$this->setError(Text::_('COM_DOCUMENTOS_ERROR_REVISION_BEFORE_PUBLICATION'));

			return false;
		}

		$path = DocumentosHelper::filePath((string) $this->arquivo);
		$ext  = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

		if ($path === '' || !in_array($ext, self::ALLOWED_EXTENSIONS, true) || str_contains($path, '..')) {
			$this->setError(Text::sprintf('COM_DOCUMENTOS_ERROR_FILE_TYPE', implode(', ', self::ALLOWED_EXTENSIONS)));

			return false;
		}

		// Código único
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName('codigo') . ' = ' . $db->quote($this->codigo))
			->where($db->quoteName('id') . ' != ' . (int) $this->id);

		if ($db->setQuery($query)->loadResult()) {
			$this->setError(Text::sprintf('COM_DOCUMENTOS_ERROR_CODIGO_DUPLICADO', $this->codigo));

			return false;
		}

		return parent::check();
	}

	public function store($updateNulls = true)
	{
		$now    = Factory::getDate()->toSql();
		$userId = (int) Factory::getApplication()->getIdentity()->id;

		if (!$this->id) {
			$this->created    = $now;
			$this->created_by = $userId;
		}

		$this->modified    = $now;
		$this->modified_by = $userId;

		return parent::store($updateNulls);
	}
}
