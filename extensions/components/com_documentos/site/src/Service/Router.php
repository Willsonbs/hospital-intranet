<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * URLs: /documentos e /documentos/pop-enf-042 (o alias é o código do documento).
 */
class Router extends RouterView
{
	private DatabaseInterface $db;

	/**
	 * Assinatura igual à usada pelo RouterFactory: (app, menu, categoryFactory, db).
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu, $categoryFactory = null, ?DatabaseInterface $db = null)
	{
		$this->db = $db ?? \Joomla\CMS\Factory::getContainer()->get(DatabaseInterface::class);

		$list = new RouterViewConfiguration('documentos');
		$this->registerView($list);

		$item = new RouterViewConfiguration('documento');
		$item->setKey('id')->setParent($list);
		$this->registerView($item);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	public function getDocumentoSegment($id, $query)
	{
		$id = (int) $id;

		$alias = $this->db->setQuery(
			$this->db->getQuery(true)
				->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__hospital_documentos'))
				->where($this->db->quoteName('id') . ' = :id')
				->bind(':id', $id, ParameterType::INTEGER)
		)->loadResult();

		return [$id => $alias ?: (string) $id];
	}

	public function getDocumentoId($segment, $query)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__hospital_documentos'))
			->where($this->db->quoteName('alias') . ' = :alias')
			->bind(':alias', $segment);

		return (int) $this->db->setQuery($query)->loadResult() ?: false;
	}
}
