<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\Model;

\defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Lista pública. Filtros via GET (q, cat, setor, status) — funcionam sem JavaScript.
 */
class DocumentosModel extends ListModel
{
	protected function populateState($ordering = null, $direction = null)
	{
		$app    = Factory::getApplication();
		$input  = $app->getInput();
		$params = $app->getParams();

		// Categorias do item de menu (ex.: Protocolos = Protocolos + POPs); vazio = todas
		$menuCats = array_values(array_filter(array_map('intval', (array) $params->get('catids', []))));
		$cat      = $input->getInt('cat');
		$status   = $input->getCmd('status');

		$this->setState('params', $params);
		$this->setState('filter.menu_cats', $menuCats);
		$this->setState('filter.cat', (!$menuCats || in_array($cat, $menuCats, true)) ? $cat : 0);
		$this->setState('filter.search', trim($input->getString('q', '')));
		$this->setState('filter.setor', trim($input->getString('setor', '')));
		$this->setState('filter.status', in_array($status, DocumentosHelper::STATUS, true) ? $status : '');

		$this->setState('list.limit', (int) ComponentHelper::getParams('com_documentos')->get('list_limit', 20));
		$this->setState('list.start', $input->getUint('limitstart', 0));
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . implode(',', $this->getState('filter.menu_cats'));

		foreach (['cat', 'search', 'setor', 'status'] as $filter) {
			$id .= ':' . $this->getState('filter.' . $filter);
		}

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('a.id, a.catid, a.codigo, a.titulo, a.alias, a.setor, a.versao, a.status, a.arquivo')
			->select('a.data_publicacao, a.data_revisao, a.publico_alvo')
			->select($db->quoteName('c.title', 'category_title'))
			->from($db->quoteName('#__hospital_documentos', 'a'))
			->join('INNER', $db->quoteName('#__categories', 'c'), 'c.id = a.catid AND c.published = 1')
			->where('a.state = 1');

		if ($menuCats = $this->getState('filter.menu_cats')) {
			$query->whereIn('a.catid', $menuCats);
		}

		if ($cat = (int) $this->getState('filter.cat')) {
			$query->where('a.catid = :cat')->bind(':cat', $cat, ParameterType::INTEGER);
		}

		if ($setor = (string) $this->getState('filter.setor')) {
			$query->where('a.setor = :setor')->bind(':setor', $setor);
		}

		if ($status = (string) $this->getState('filter.status')) {
			$query->where('a.status = :status')->bind(':status', $status);
		} else {
			$query->whereIn('a.status', DocumentosHelper::STATUS_PUBLIC, ParameterType::STRING);
		}

		$search = (string) $this->getState('filter.search');

		if ($search !== '') {
			$like = '%' . $search . '%';
			$query->where('(a.codigo LIKE :s1 OR a.titulo LIKE :s2 OR a.descricao LIKE :s3 OR a.setor LIKE :s4)')
				->bind(':s1', $like)
				->bind(':s2', $like)
				->bind(':s3', $like)
				->bind(':s4', $like);
		}

		$query->order('a.data_publicacao DESC, a.id DESC');

		return $query;
	}

	/**
	 * Categorias disponíveis para o filtro (respeitando as categorias do menu).
	 *
	 * @return \stdClass[]
	 */
	public function getCategories(): array
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('id, title')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_documentos'))
			->where('published = 1')
			->order('lft');

		if ($menuCats = $this->getState('filter.menu_cats')) {
			$query->whereIn('id', $menuCats);
		}

		return $db->setQuery($query)->loadObjectList();
	}
}
