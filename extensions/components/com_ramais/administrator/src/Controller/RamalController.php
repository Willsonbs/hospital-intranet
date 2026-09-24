<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

class RamalController extends FormController
{
	protected $text_prefix = 'COM_RAMAIS';

	protected $view_list = 'ramais';
}
