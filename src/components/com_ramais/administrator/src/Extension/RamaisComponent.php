<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Extension;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\MVCComponent;

\defined('_JEXEC') or die;

/**
 * Diretório de ramais: setor, ramal e localização.
 */
final class RamaisComponent extends MVCComponent implements RouterServiceInterface
{
    use RouterServiceTrait;
}
