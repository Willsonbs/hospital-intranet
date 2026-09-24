<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Site\View\Documentos;

\defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;
	protected $categories;
	protected $setores;

	public function display($tpl = null)
	{
		$model            = $this->getModel();
		$this->items      = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state      = $model->getState();
		$this->params     = $this->state->get('params');
		$this->categories = $model->getCategories();
		$this->setores    = DocumentosHelper::setores();

		$this->setDocumentTitle($this->params->get('page_title', ''));

		parent::display($tpl);
	}
}
