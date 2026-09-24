<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\View\Ramal;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

final class HtmlView extends BaseHtmlView
{
    protected $form;

    protected $item;

    public function display($tpl = null): void
    {
        $model = $this->getModel();
        $model->setUseExceptions(true);

        $this->form = $model->getForm();
        $this->item = $model->getItem();

        $this->addToolbar();

        parent::display($tpl);
    }

    private function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew   = (int) $this->item->id === 0;
        $canDo   = ContentHelper::getActions('com_ramais');
        $toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title(Text::_($isNew ? 'COM_RAMAIS_RAMAL_NEW' : 'COM_RAMAIS_RAMAL_EDIT'), 'phone');

        if ($canDo->get($isNew ? 'core.create' : 'core.edit')) {
            $toolbar->apply('ramal.apply');
            $toolbar->save('ramal.save');
        }

        if ($canDo->get('core.create')) {
            $toolbar->save2new('ramal.save2new');
        }

        $toolbar->cancel('ramal.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
