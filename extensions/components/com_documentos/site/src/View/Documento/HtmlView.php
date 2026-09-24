<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\View\Documento;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	protected $item;
	protected $params;

	public function display($tpl = null)
	{
		$model        = $this->getModel();
		$this->item   = $model->getItem();
		$this->params = $model->getState('params');

		$this->setDocumentTitle($this->item->codigo . ' — ' . $this->item->titulo);

		$pathway = Factory::getApplication()->getPathway();
		$pathway->addItem($this->item->codigo);

		parent::display($tpl);
	}
}
