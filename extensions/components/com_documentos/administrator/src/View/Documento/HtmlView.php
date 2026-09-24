<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\View\Documento;

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
		$canDo = ContentHelper::getActions('com_documentos', 'category', (int) $this->item->catid);
		$user  = $this->getCurrentUser();

		ToolbarHelper::title(Text::_($isNew ? 'COM_DOCUMENTOS_NEW' : 'COM_DOCUMENTOS_EDIT'), 'file-alt');

		$canSave = $isNew
			? ($canDo->get('core.create') || $user->getAuthorisedCategories('com_documentos', 'core.create'))
			: $canDo->get('core.edit');

		if ($canSave) {
			ToolbarHelper::apply('documento.apply');
			ToolbarHelper::save('documento.save');
			ToolbarHelper::save2new('documento.save2new');
		}

		ToolbarHelper::cancel('documento.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}
