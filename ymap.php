<?php

$intLatitude=($_REQUEST['Latitude']);
$intLongtitude=($_REQUEST['Longtitude']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
<title>Yahoo Maps</title>
<script type="text/javascript"
src="http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=Oix9A7jV34FHwwLNGvW7xlU1.iE8sOER9NXvjEjO7ZKuW_Ph.2vfDk.Rbh4Ny8jB"></script>
<style type="text/css">
#map{
height: 450px;
width: 600px;
}
</style>
</head>
<body>
<div id="map"></div>

<script type="text/javascript">
	// Create a map object
	var map = new YMap(document.getElementById('map'));

	// Add map type control
	map.addTypeControl();

	// Add map zoom (long) control
	map.addZoomLong();

	// Add the Pan Control
	map.addPanControl();

	// Set map type to either of: YAHOO_MAP_SAT, YAHOO_MAP_HYB, YAHOO_MAP_REG
	map.setMapType(YAHOO_MAP_SAT);

var myPoint = new YGeoPoint(<?php echo $intLatitude ?>,<?php echo $intLongtitude ?>); 

	// Display the map centered on a geocoded location
	map.drawZoomAndCenter(myPoint, 3);
</script>
</body>
</html>