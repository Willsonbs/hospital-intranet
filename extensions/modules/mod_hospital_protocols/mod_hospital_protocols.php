<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_protocols
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

/** @var \Joomla\Registry\Registry $params */
/** @var \stdClass $module */

$items = ModHospitalProtocolsHelper::getItems($params);

if (!$items) {
	return;
}

require ModuleHelper::getLayoutPath('mod_hospital_protocols', $params->get('layout', 'default'));
