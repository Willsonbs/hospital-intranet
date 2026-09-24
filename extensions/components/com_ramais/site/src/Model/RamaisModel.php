<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Lista pública: somente ramais ativos, sem paginação.
 * Pesquisa (?q=) e ordenação (?sort=&dir=) também funcionam sem JavaScript.
 */
class RamaisModel extends ListModel
{
	public const SORTABLE = ['setor', 'ramal', 'localizacao'];

	protected function populateState($ordering = null, $direction = null)
	{
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$input  = $app->getInput();

		$default = $params->get('default_ordering', 'setor') === 'custom' ? 'custom' : 'setor';
		$sort    = $input->getCmd('sort', $default);

		$this->setState('params', $params);
		$this->setState('filter.search', trim($input->getString('q', '')));
		$this->setState('list.sort', in_array($sort, self::SORTABLE, true) || $sort === 'custom' ? $sort : 'setor');
		$this->setState('list.dir', strtolower($input->getCmd('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC');
		$this->setState('list.start', 0);
		$this->setState('list.limit', 0);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'setor', 'ramal', 'localizacao']))
			->from($db->quoteName('#__hospital_ramais'))
			->where($db->quoteName('state') . ' = 1');

		$search = (string) $this->getState('filter.search');

		if ($search !== '') {
			$like = '%' . $search . '%';
			$query->where('(' . $db->quoteName('setor') . ' LIKE :s1 OR '
				. $db->quoteName('ramal') . ' LIKE :s2 OR '
				. $db->quoteName('localizacao') . ' LIKE :s3)')
				->bind(':s1', $like)
				->bind(':s2', $like)
				->bind(':s3', $like);
		}

		$sort = $this->getState('list.sort');
		$dir  = $this->getState('list.dir');

		if ($sort === 'custom') {
			$query->order($db->quoteName('ordering') . ' ASC')->order($db->quoteName('setor') . ' ASC');
		} elseif ($sort === 'ramal') {
			$query->order('LENGTH(' . $db->quoteName('ramal') . ') ' . $dir)->order($db->quoteName('ramal') . ' ' . $dir);
		} else {
			$query->order($db->quoteName($sort) . ' ' . $dir)->order($db->quoteName('setor') . ' ASC');
		}

		return $query;
	}
}
