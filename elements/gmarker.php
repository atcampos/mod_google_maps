<?php
/**
 * @package		Google Maps
 * @subpackage	mod_google_maps
 * @copyright	Copyright (C) AtomTech, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE . '/modules/mod_google_maps/elements/GElement.php');

class JElementGMarker extends JElement{
	var $_name = "GMarker";
}

/**
 * Form Field GMarker class.
 *
 * @package		Google Maps
 * @subpackage	mod_google_maps
 * @since		2.5
 */
class JFormFieldGMarker extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'GMarker';

	/**
	 * Javascript map initialization
	 * Javascript variable 'marker' must be defined.
	 */
	function addJavascript(&$params){
		$doc = JFactory::getDocument();
		$lat = $params->get('lat');
		$long = $params->get('longitude');
		$js =<<<EOL
			var marker;
			function updateMarker(element){
				var lat = $('jform_params_lat').value;
				var lng = $('jform_params_lng').value;
				var opts = { draggable : true, bouncy: true };

				if(element.checked == true){
						opts.title = $('jform_params_marker_title').value;
						opts.icon = $('jform_params_marker_icon').value;
						opts.shadow = $('jform_params_marker_shadow').value;
						opts.map = map;
						opts.position = new google.maps.LatLng( lat, lng);
						marker = new google.maps.Marker(opts);
						google.maps.event.addListener(marker, 'drag', function(latlng){
							var latlng = this.getPosition();
							$('jform_params_marker_lat').value = latlng.lat();
							$('jform_params_marker_lng').value = latlng.lng();
						});

						$('jform_params_marker_lat').value = lat;
						$('jform_params_marker_lng').value = lng;

			  	}else{
			  		if(marker){
			  			marker.setVisible(false);
			  			delete marker;
			  			marker = null;

			  		}
			  	}
			}
EOL;

		$doc->addScriptDeclaration($js);
	}

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 * @since	2.5
	 */
	protected function getInput()
	{
		$params =& GElement::getParameters();

		$this->addJavascript($params);
		$marker = ($params->get('marker', '')) ? 'checked' : '' ;
		
		$html = '<div>';
		$html .= '<input type="checkbox" id="paramsmarker" name="jform[params][marker]" ' . $marker . ' onclick="updateMarker(this);" value="1" />';

		return $html;
	}
 }