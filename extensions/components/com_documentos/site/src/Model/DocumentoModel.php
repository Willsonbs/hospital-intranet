<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

class DocumentoModel extends ItemModel
{
	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('documento.id', $app->getInput()->getInt('id'));
		$this->setState('params', $app->getParams());
	}

	public function getItem($pk = null)
	{
		$pk = (int) ($pk ?: $this->getState('documento.id'));

		if (isset($this->_item[$pk])) {
			return $this->_item[$pk];
		}

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('a.*')
			->select($db->quoteName('c.title', 'category_title'))
			->from($db->quoteName('#__hospital_documentos', 'a'))
			->join('INNER', $db->quoteName('#__categories', 'c'), 'c.id = a.catid AND c.published = 1')
			->where('a.id = :id')
			->where('a.state = 1')
			->bind(':id', $pk, ParameterType::INTEGER);

		$item = $db->setQuery($query)->loadObject();

		if (!$item) {
			throw new \Exception(Text::_('COM_DOCUMENTOS_ERROR_NOT_FOUND'), 404);
		}

		return $this->_item[$pk] = $item;
	}
}
