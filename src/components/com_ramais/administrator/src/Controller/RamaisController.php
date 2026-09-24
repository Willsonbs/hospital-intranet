<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

\defined('_JEXEC') or die;

/** Ações em lote na lista: ativar, desativar, lixeira, excluir. */
final class RamaisController extends AdminController
{
    protected $text_prefix = 'COM_RAMAIS_RAMAIS';

    public function getModel($name = 'Ramal', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
