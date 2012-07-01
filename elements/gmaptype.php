<?php
/**
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Form Field GmapType class.
 *
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @since       2.5
 */
class JFormFieldGMapType extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'gmaptype';

	/**
	 * Method to get the field input.
	 *
	 * @return  string  The field input.
	 * 
	 * @since   2.5
	 */
	protected function getInput()
	{
		$options[] = array('value' => 'ROADMAP', 'text' => JText::_('MOD_GOOGLE_MAPS_OPTION_MAP'));
		$options[] = array('value' => 'SATELLITE', 'text' => JText::_('MOD_GOOGLE_MAPS_OPTION_SATELLITE'));
		$options[] = array('value' => 'HYBRID', 'text' => JText::_('MOD_GOOGLE_MAPS_OPTION_HYBRID'));
		$options[] = array('value' => 'TERRAIN', 'text' => JText::_('MOD_GOOGLE_MAPS_OPTION_TERRAIN'));

		$onchange = 'onchange="map.setMapTypeId(eval(\'google.maps.MapTypeId.\' + this.options[this.selectedIndex].value))"';

		return JHTML::_('select.genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value);
	}
}
