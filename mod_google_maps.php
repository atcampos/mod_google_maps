<?php
/**
 * @package		Google Maps
 * @subpackage	mod_google_maps
 * @copyright	Copyright (C) AtomTech, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$introtext = htmlspecialchars($params->get('introtext'));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_google_maps', $params->get('layout', 'default'));
