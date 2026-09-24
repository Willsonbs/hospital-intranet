<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\View\Documentos;

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
		$canDo = ContentHelper::getActions('com_documentos', 'category', (int) $this->state->get('filter.catid'));
		$user  = $this->getCurrentUser();

		ToolbarHelper::title(Text::_('COM_DOCUMENTOS_MANAGER'), 'file-alt');

		if ($canDo->get('core.create') || $user->getAuthorisedCategories('com_documentos', 'core.create')) {
			ToolbarHelper::addNew('documento.add');
		}

		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::publish('documentos.publish', 'JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('documentos.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList('COM_DOCUMENTOS_CONFIRM_DELETE', 'documentos.delete');
		}

		if ($user->authorise('core.admin', 'com_documentos') || $user->authorise('core.options', 'com_documentos')) {
			ToolbarHelper::preferences('com_documentos');
		}
	}
}
