<?php
/**
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$introtext = htmlspecialchars($params->get('introtext'));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_google_maps', $params->get('layout', 'default'));
