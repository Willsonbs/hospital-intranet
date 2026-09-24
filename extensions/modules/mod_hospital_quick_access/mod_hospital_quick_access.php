<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_quick_access
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

/** @var \Joomla\Registry\Registry $params */
/** @var \stdClass $module */

$items = ModHospitalQuickAccessHelper::getItems($params);

if (!$items) {
	return;
}

require ModuleHelper::getLayoutPath('mod_hospital_quick_access', $params->get('layout', 'default'));
