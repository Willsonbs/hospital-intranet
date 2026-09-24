<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Site\View\Ramais;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $state;
	protected $params;

	public function display($tpl = null)
	{
		$model        = $this->getModel();
		$this->items  = $model->getItems();
		$this->state  = $model->getState();
		$this->params = $this->state->get('params');

		$menu  = Factory::getApplication()->getMenu()->getActive();
		$title = $this->params->get('page_title', $menu ? $menu->title : Text::_('COM_RAMAIS_PAGE_TITLE'));
		$this->setDocumentTitle($title);

		parent::display($tpl);
	}
}
