<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

class DocumentoController extends FormController
{
	protected $text_prefix = 'COM_DOCUMENTOS';

	protected $view_list = 'documentos';

	/**
	 * Criar exige permissão na categoria escolhida (ou no componente).
	 */
	protected function allowAdd($data = [])
	{
		$categoryId = (int) ($data['catid'] ?? $this->input->getInt('filter_category_id'));

		if ($categoryId) {
			return $this->app->getIdentity()->authorise('core.create', 'com_documentos.category.' . $categoryId);
		}

		return parent::allowAdd($data);
	}

	/**
	 * Editar exige permissão na categoria do documento.
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		$recordId = (int) ($data[$key] ?? 0);

		if ($recordId) {
			$item = $this->getModel()->getItem($recordId);

			if ($item && $item->catid) {
				return $this->app->getIdentity()->authorise('core.edit', 'com_documentos.category.' . (int) $item->catid);
			}
		}

		return parent::allowEdit($data, $key);
	}
}
