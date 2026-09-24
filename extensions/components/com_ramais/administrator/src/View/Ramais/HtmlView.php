<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\View\Ramais;

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;

	public function display($tpl = null)
	{
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_ramais');

		ToolbarHelper::title(Text::_('COM_RAMAIS_MANAGER'), 'phone');

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('ramal.add');
		}

		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::publish('ramais.publish', 'COM_RAMAIS_ACTIVATE', true);
			ToolbarHelper::unpublish('ramais.unpublish', 'COM_RAMAIS_DEACTIVATE', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList('COM_RAMAIS_CONFIRM_DELETE', 'ramais.delete');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options')) {
			ToolbarHelper::preferences('com_ramais');
		}
	}
}
