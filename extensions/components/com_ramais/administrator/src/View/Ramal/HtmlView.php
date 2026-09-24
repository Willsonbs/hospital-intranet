<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\View\Ramal;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $form;
	protected $item;

	public function display($tpl = null)
	{
		$model      = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		Factory::getApplication()->getInput()->set('hidemainmenu', true);

		$isNew = empty($this->item->id);
		$canDo = ContentHelper::getActions('com_ramais');

		ToolbarHelper::title(Text::_($isNew ? 'COM_RAMAIS_NEW' : 'COM_RAMAIS_EDIT'), 'phone');

		if ($isNew ? $canDo->get('core.create') : $canDo->get('core.edit')) {
			ToolbarHelper::apply('ramal.apply');
			ToolbarHelper::save('ramal.save');
		}

		if ($canDo->get('core.create')) {
			ToolbarHelper::save2new('ramal.save2new');
		}

		ToolbarHelper::cancel('ramal.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}
