<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

\defined('_JEXEC') or die;

/** Um ramal: formulário e gravação. */
final class RamalModel extends AdminModel
{
    public $typeAlias = 'com_ramais.ramal';

    protected $text_prefix = 'COM_RAMAIS';

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_ramais.ramal', 'ramal', ['control' => 'jform', 'load_data' => $loadData]) ?: false;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_ramais.edit.ramal.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_ramais.ramal', $data);

        return $data;
    }

    /** Excluir definitivamente só a partir da lixeira. */
    protected function canDelete($record)
    {
        return !empty($record->id) && (int) $record->state === -2 && parent::canDelete($record);
    }
}
