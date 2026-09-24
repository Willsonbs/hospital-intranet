<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

class Category extends Categories
{
	public function __construct($options = [])
	{
		$options['table']      = '#__hospital_documentos';
		$options['extension']  = 'com_documentos';
		$options['statefield'] = 'state';

		parent::__construct($options);
	}
}
