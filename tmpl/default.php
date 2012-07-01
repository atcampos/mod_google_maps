<?php
/**
 * @package     Google_Maps
 * @subpackage  mod_google_maps
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$doc = &JFactory::getDocument();
$width = $params->get('width', 160);
$height = $params->get('height', 120);
$lat = $params->get('lat', 49);
$long = $params->get('lng', -122);
$zoom = $params->get('zoom', 3);
$mapName = $params->get('mapName', 'map');
$mapType = $params->get('mapType', 'ROADMAP');
$js = "http://maps.google.com/maps/api/js?sensor=false";

$doc->addScript($js);
$mapOptions = '';
$markerOptions = '';

if ($params->get('marker'))
{
	$title = $params->get('marker_title', '');

	if ($marker_icon = $params->get('marker_icon') != '')
	{
		$marker_icon = $params->get('marker_icon');
	}

	if ($marker_shadow = $params->get('marker_shadow') != '')
	{
		$marker_shadow = $params->get('marker_shadow');
	}

	$marker_lat = $params->get('marker_lat');
	$marker_lng = $params->get('marker_lng');
	$markerOptions = <<<EOL

	var opts = new Object;
	opts.title = "{$title}";
	opts.icon = "{$marker_icon}";
	opts.shadow = "{$marker_shadow}";
	opts.position = new google.maps.LatLng({$marker_lat}, {$marker_lng});
	opts.map = $mapName;
	marker = new google.maps.Marker(opts);
EOL;
}
$navControls = true;
if ($params->get('static') || $params->get('navControls', false) == 0)
{
	$mapOptions .= ',disableDefaultUI: false' . PHP_EOL;
	$mapOptions .= ',streetViewControl: false' . PHP_EOL;
	$navControls = false;
}
if ($params->get('smallmap'))
{
	$mapOptions .= ', navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL} ' . PHP_EOL;
	$navControls = true;
}

if (!$navControls)
{
	$mapOptions .= ',navigationControl: false' . PHP_EOL;
}

if ($params->get('static'))
{
	$mapOptions .= ', draggable: false' . PHP_EOL;
}

$mapTypeControl = $params->get('mapTypeControl',false) ? 'true' : 'false';

$mapOptions .= ",mapTypeControl: {$mapTypeControl}" . PHP_EOL;

$script = <<<EOL
	google.maps.event.addDomListener(window, 'load', {$mapName}load);

    function {$mapName}load() {
		var options = {
			zoom : {$zoom},
			center: new google.maps.LatLng({$lat}, {$long}),
			mapTypeId: google.maps.MapTypeId.{$mapType}
			{$mapOptions}
		}

        var {$mapName} = new google.maps.Map(document.getElementById("{$mapName}"), options);
		{$markerOptions}
    }

EOL;

JHTML::_('behavior.mootools');

$doc->addScriptDeclaration($script);
?>
<div class="map<?php echo $moduleclass_sfx; ?>">
	<?php if ($introtext): ?>
		<p><?php echo $introtext; ?></p>
	<?php endif ?>
	<div id="<?php echo $mapName; ?>" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>
	
</div>