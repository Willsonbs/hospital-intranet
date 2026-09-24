<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\View\Ramais;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

final class HtmlView extends BaseHtmlView
{
    public $filterForm;

    public $activeFilters = [];

    protected $items = [];

    protected $pagination;

    protected $state;

    public function display($tpl = null): void
    {
        $model = $this->getModel();
        $model->setUseExceptions(true);

        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        $this->addToolbar();

        $this->filterForm
            ->addControlField('task')
            ->addControlField('boxchecked', '0');

        parent::display($tpl);
    }

    private function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_ramais');
        $toolbar = $this->getDocument()->getToolbar();
        $trashed = (string) $this->state->get('filter.state') === '-2';

        ToolbarHelper::title(Text::_('COM_RAMAIS_MANAGER'), 'phone');

        if ($canDo->get('core.create')) {
            $toolbar->addNew('ramal.add');
        }

        if ($this->items && $canDo->get('core.edit.state')) {
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish('ramais.publish', 'COM_RAMAIS_TOOLBAR_ACTIVATE')->listCheck(true);
            $childBar->unpublish('ramais.unpublish', 'COM_RAMAIS_TOOLBAR_DEACTIVATE')->listCheck(true);
            $childBar->checkin('ramais.checkin')->listCheck(true);

            if (!$trashed) {
                $childBar->trash('ramais.trash')->listCheck(true);
            }
        }

        if ($trashed && $this->items && $canDo->get('core.delete')) {
            $toolbar->delete('ramais.delete', 'JTOOLBAR_DELETE_FROM_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            $toolbar->preferences('com_ramais');
        }
    }
}
