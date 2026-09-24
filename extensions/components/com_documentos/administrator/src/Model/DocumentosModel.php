<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class DocumentosModel extends ListModel
{
	public function __construct($config = [])
	{
		$config['filter_fields'] ??= [
			'id', 'a.id',
			'codigo', 'a.codigo',
			'titulo', 'a.titulo',
			'catid', 'a.catid', 'category_title',
			'setor', 'a.setor',
			'status', 'a.status',
			'state', 'a.state',
			'data_publicacao', 'a.data_publicacao',
			'data_revisao', 'a.data_revisao',
		];

		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.data_publicacao', $direction = 'DESC')
	{
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		foreach (['search', 'state', 'catid', 'status', 'setor'] as $filter) {
			$id .= ':' . $this->getState('filter.' . $filter);
		}

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('a.*')
			->select($db->quoteName('c.title', 'category_title'))
			->from($db->quoteName('#__hospital_documentos', 'a'))
			->join('LEFT', $db->quoteName('#__categories', 'c'), 'c.id = a.catid');

		$state = (string) $this->getState('filter.state');

		if (is_numeric($state)) {
			$state = (int) $state;
			$query->where('a.state = :state')->bind(':state', $state, ParameterType::INTEGER);
		}

		if ($catid = (int) $this->getState('filter.catid')) {
			$query->where('a.catid = :catid')->bind(':catid', $catid, ParameterType::INTEGER);
		}

		if ($status = (string) $this->getState('filter.status')) {
			$query->where('a.status = :status')->bind(':status', $status);
		}

		if ($setor = (string) $this->getState('filter.setor')) {
			$query->where('a.setor = :setor')->bind(':setor', $setor);
		}

		$search = trim((string) $this->getState('filter.search'));

		if ($search !== '') {
			$like = '%' . $search . '%';
			$query->where('(a.codigo LIKE :s1 OR a.titulo LIKE :s2 OR a.setor LIKE :s3 OR a.autor LIKE :s4)')
				->bind(':s1', $like)
				->bind(':s2', $like)
				->bind(':s3', $like)
				->bind(':s4', $like);
		}

		$query->order(
			$db->escape($this->getState('list.ordering', 'a.data_publicacao')) . ' ' . $db->escape($this->getState('list.direction', 'DESC'))
		);

		return $query;
	}
}
