<?php
/**
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/modules/mod_google_maps/elements/GElement.php';

/**
 * Form Field Gmap class.
 *
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @since       2.5
 */
class JFormFieldGMap extends JFormField{

	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'GMap';

	/**
	 * Javascript map initialization
	 * 
	 * @param   float   $lat      The latitude.
	 * @param   float   $lng      The Longitude.
	 * @param   int     $zoom     The zoom level.
	 * @param   string  $marker   The marker name.
	 * @param   array   &$params  The custom params.
	 * 
	 * @return  void
	 * 
	 * @since   2.5
	 */
	public function initMap($lat, $lng, $zoom, $marker, &$params)
	{
		$doc = &JFactory::getDocument();
		$doc->addScript('http://maps.google.com/maps/api/js?sensor=false');

		// Get params
		$mapType = $params->get('mapType', 'ROADMAP');
		$marker_lat = $params->get('marker_lat');
		$marker_lng = $params->get('marker_lng');

		if ($marker)
		{
			$markerTitle = $params->get('marker_title');
			$markerIcon = $params->get('marker_icon');
			$markerShadow = $params->get('marker_shadow');

			$markerScript = <<<EOL
			var opts = { draggable : true, bouncy: true };
			opts.title = "{$markerTitle}";
			opts.icon = "../{$markerIcon}";
			opts.shadow = "../{$markerShadow}";
			opts.position = new google.maps.LatLng($marker_lat, $marker_lng);
			opts.map = map
			marker = new google.maps.Marker(opts);
			google.maps.event.addListener(marker, 'drag', function(latlng){
				var latlng = this.getPosition();
				$('jform_params_marker_lat').value = latlng.lat();
				$('jform_params_marker_lng').value = latlng.lng();
			});
EOL;
		}

		$onload = <<<EOL
			var map;
			var marker;

			google.maps.event.addDomListener(window,'load', load );
		    function load() {
			 	var mapOptions = {
			 	    center : new google.maps.LatLng({$lat}, {$lng}),
			 	    zoom : {$zoom},
			 	    mapTypeId: google.maps.MapTypeId.{$mapType},
      				navigationControl: true,
      				mapTypeControl: false
  				}
		        map = new google.maps.Map(document.getElementById("map"), mapOptions);

		        google.maps.event.addListener( map , 'dragend', updateLatLong);
				google.maps.event.addListener( map , 'zoom_changed', updateZoom);
				google.maps.event.addListener( map , 'drag', updateLatLong);
				$markerScript

				$('jform_params_zoom').addEvent('keyup', function(){
					value = parseInt(this.value);
					if(value)
						map.setZoom(value);
				});

				$('search_location').addEvent('keydown', function(event) {
      				if(event.key == "enter") {
         				searchlocation();
      				}
   				});

				$('searchBtn').addEvent('click', searchlocation	);

		    }

		    function searchlocation(){
				var value = $('search_location').value;
				var request = { address : value };
				if(!value) return;
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode(request, function ( results ,status ){
					if (status == google.maps.GeocoderStatus.OK) {
						latlng = results[0].geometry.location;
				        map.setCenter(latlng);
				        $('jform_params_lat').value = latlng.lat();
				        $('jform_params_lng').value = latlng.lng();
				        $('jform_params_marker_title').value = results[0].formatted_address;
						$('jform_params_marker_icon').value = results[0];
					    $('jform_params_marker_shadow').value = results[0];
				        $('jform_params_marker_lat').value = latlng.lat();
				        $('jform_params_marker_lng').value = latlng.lng();
				        if(marker){
							marker.setTitle(results[0].formatted_address);
							marker.setIcon(results[0].formatted_address);
							marker.setShadow(results[0].formatted_address);
							marker.setPosition(latlng);
				        }else{
					        marker = new google.maps.Marker({
					        	map: map,
					            position: latlng,
					            title : results[0].formatted_address,
								icon : results[0].formatted_address,
								shadow : results[0].formatted_address
					        });
				        }
				        $('paramsmarker').checked = true;
				    } else {
				    	alert("Geocode was not successful for the following reason: " + status);
				    }

				});
			}

			function updateZoom(){
				$('jform_params_zoom').value = map.getZoom();
			}

			function updateLatLong(){
				var center = map.getCenter();
				var lat = center.lat();
				var lng = center.lng();
				$('jform_params_lat').value = lat;
				$('jform_params_lng').value = lng;
			}
EOL;

		$doc->addScriptDeclaration($onload);
	}

	/**
	 * Method to get the field input.
	 *
	 * @return	string  The field input.
	 * 
	 * @since	2.5
	 */
	protected function getInput()
	{
		// Get params
		$params = &GElement::getParameters();

		// Initialise variables
		$lat = $params->get('lat', -14.235004);
		$long = $params->get('lng', -51.925279);
		$zoom = $params->get('zoom', 3);
		$width = $params->get('width', 425);
		$height = $params->get('height', 350);
		$marker = $params->get('marker');
		$markerTitle = $params->get('marker_info', '');
		$markerIcon = $params->get('marker_info', '');
		$markerShadow = $params->get('marker_info', '');

		// Initialise map
		$this->initMap($lat, $long, $zoom, $marker, $params);

		$search = JText::_("JSEARCH_FILTER_SUBMIT");
		$searchMap = JText::_('MOD_GOOGLE_MAPS_FIELD_SEARCH_LOCATION');

		$elements = <<<EOL
		<div style="clear:left;">
			<div id="map" style="padding: 10px,margin:4px 4px 10px; width: {$width}px; height: {$height}px"> </div>
			<label for="search_location" >$searchMap</label>
			<input type="text" size="45" id="search_location" name="search_location" />
			<input type="button" value="$search" id="searchBtn" />
		</div>
EOL;

		return $elements;
	}

}
?>
<script type="text/javascript">
	/**
	 * Events for adjusting the size of the google map.
	 */
	window.addEvent('load', function () {
	    var el = $('jform_params_width');
	    el.addEvent('keyup', function () {
	        adjustDimensions(this);
	    });
	    el = $('jform_params_height');
	    el.addEvent('keyup', function () {
	        adjustDimensions(this);
	    });

	});

	/**
	 * Adjust the google map size.
	 * @param el Div of the Google map.
	 */
	function adjustDimensions(el) {
	    if (el.value.length > 2) {
	        var mapDiv = $('map');
	        var width = parseInt($('jform_params_width').value) || 100;
	        var height = parseInt($('jform_params_height').value) || 100;
	        mapDiv.style.width = width + 'px';
	        mapDiv.style.height = height + 'px';
	    }
	}
</script>