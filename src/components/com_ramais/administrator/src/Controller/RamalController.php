<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

\defined('_JEXEC') or die;

/** Formulário de um ramal: salvar, salvar e novo, cancelar. */
final class RamalController extends FormController
{
    protected $text_prefix = 'COM_RAMAIS_RAMAL';
}
