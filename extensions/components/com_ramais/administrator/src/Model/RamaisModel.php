<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class RamaisModel extends ListModel
{
	public function __construct($config = [])
	{
		$config['filter_fields'] ??= [
			'id', 'a.id',
			'setor', 'a.setor',
			'ramal', 'a.ramal',
			'localizacao', 'a.localizacao',
			'state', 'a.state',
			'ordering', 'a.ordering',
		];

		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.setor', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('a.*')
			->from($db->quoteName('#__hospital_ramais', 'a'));

		$state = (string) $this->getState('filter.state');

		if (is_numeric($state)) {
			$state = (int) $state;
			$query->where($db->quoteName('a.state') . ' = :state')->bind(':state', $state, ParameterType::INTEGER);
		}

		$search = trim((string) $this->getState('filter.search'));

		if ($search !== '') {
			$like = '%' . $search . '%';
			$query->where('(' . $db->quoteName('a.setor') . ' LIKE :s1 OR '
				. $db->quoteName('a.ramal') . ' LIKE :s2 OR '
				. $db->quoteName('a.localizacao') . ' LIKE :s3)')
				->bind(':s1', $like)
				->bind(':s2', $like)
				->bind(':s3', $like);
		}

		$query->order(
			$db->escape($this->getState('list.ordering', 'a.setor')) . ' ' . $db->escape($this->getState('list.direction', 'ASC'))
		);

		return $query;
	}
}
