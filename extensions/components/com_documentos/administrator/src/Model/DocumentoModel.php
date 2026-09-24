<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class DocumentoModel extends AdminModel
{
	public $typeAlias = 'com_documentos.documento';

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_documentos.documento', 'documento', ['control' => 'jform', 'load_data' => $loadData]);

		return $form ?: false;
	}

	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_documentos.edit.documento.data', []);

		if (!$data) {
			$data = $this->getItem();

			// Novo documento: pré-seleciona a categoria do filtro e a data de hoje
			if (!$data->id) {
				$data->catid           = $app->getUserState('com_documentos.documentos.filter.catid') ?: $data->catid;
				$data->data_publicacao = Factory::getDate()->format('Y-m-d');
			}
		}

		return $data;
	}

	/**
	 * Permissões por categoria (ex.: Qualidade gerencia Protocolos/POPs).
	 */
	protected function canDelete($record)
	{
		return !empty($record->id)
			&& $this->getCurrentUser()->authorise('core.delete', 'com_documentos.category.' . (int) $record->catid);
	}

	protected function canEditState($record)
	{
		if (!empty($record->catid)) {
			return $this->getCurrentUser()->authorise('core.edit.state', 'com_documentos.category.' . (int) $record->catid);
		}

		return parent::canEditState($record);
	}
}
