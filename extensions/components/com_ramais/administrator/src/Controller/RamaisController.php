<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class RamaisController extends AdminController
{
	protected $text_prefix = 'COM_RAMAIS';

	public function getModel($name = 'Ramal', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
}
