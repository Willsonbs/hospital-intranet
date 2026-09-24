<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\MVCComponent;

class DocumentosComponent extends MVCComponent implements CategoryServiceInterface, RouterServiceInterface
{
	use CategoryServiceTrait;
	use RouterServiceTrait;

	protected function getTableNameForSection(?string $section = null)
	{
		return 'hospital_documentos';
	}

	protected function getStateColumnForSection(?string $section = null)
	{
		return 'state';
	}
}
