<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class RamalModel extends AdminModel
{
	public $typeAlias = 'com_ramais.ramal';

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_ramais.ramal', 'ramal', ['control' => 'jform', 'load_data' => $loadData]);

		return $form ?: false;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_ramais.edit.ramal.data', []);

		return $data ?: $this->getItem();
	}
}
