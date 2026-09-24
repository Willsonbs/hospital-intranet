<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Field;

\defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Lista de setores responsáveis, mantida em Opções do componente.
 */
class SetorField extends ListField
{
	protected $type = 'Setor';

	protected function getOptions()
	{
		$options = parent::getOptions();
		$setores = DocumentosHelper::setores();

		// Mantém um valor antigo que tenha saído da lista
		if ($this->value && !in_array($this->value, $setores, true)) {
			$setores[] = $this->value;
		}

		foreach ($setores as $setor) {
			$options[] = HTMLHelper::_('select.option', $setor, $setor);
		}

		return $options;
	}
}
