<?php

/**
 * to use GoogleMaps on your domain you will have to get your own 
 * GoogleMaps API Key (register at http://www.google.com/apis/maps/signup.html)
 */
 
$str_googleMaps_APIKey	= "ABQIAAAAVlNIdNKqPnzEYqwZl8yJHxQP6_IKcf7eRVHOyk5Vcy-P1gKsWhR3XQDC6JVU-ogGhf3bfx_w4IBvKw";

$intLatitude=($_REQUEST['Latitude']);
$intLongtitude=($_REQUEST['Longtitude']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
<title>Geo marker in Google maps showing location</title>

<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=<?php echo $str_googleMaps_APIKey ?>" type="text/javascript"></script>
<script type="text/javascript">

//<![CDATA[

function Build() { }
Build.prototype = new GControl();
Build.prototype.initialize = function(map) {
  var build = document.createElement("div");
  this.setBuildStyle(build);
  build.appendChild(document.createTextNode("kruyt.org"));
  GEvent.addDomListener(build, "click", function() {
    window.open('http://kruyt.or');
  });

  map.getContainer().appendChild(build);
  return build;
}
Build.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 7));
}
Build.prototype.setBuildStyle = function(build) {
  build.style.textDecoration = "none";
  build.style.color = "#000000";
  build.style.backgroundColor = "white";
  build.style.font = "bold 9px Arial";
  build.style.border = "1px solid black";
  build.style.padding = "2px";
  build.style.marginBottom = "3px";
  build.style.textAlign = "center";
  build.style.width = "100px";
  build.style.cursor = "pointer";
}


var baseIcon = new GIcon();
baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
baseIcon.iconSize = new GSize(20, 34);
baseIcon.shadowSize = new GSize(37, 34);
baseIcon.iconAnchor = new GPoint(9, 34);

// Creates a marker whose info window displays the letter corresponding
// to the given index.
function createMarker(point, index) {
  // Create a lettered icon for this point using our icon class
  var letter = String.fromCharCode("A".charCodeAt(0) + index);
  var icon = new GIcon(baseIcon);
  icon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
  var marker = new GMarker(point, icon);
  return marker;
}

function load() {
  if (GBrowserIsCompatible()) {
    var map = new GMap2(document.getElementById("map"));
    map.setCenter(new GLatLng(<?php echo $intLatitude ?>,<?php echo $intLongtitude ?>), 13);
    map.setMapType(G_SATELLITE_MAP);
    map.addControl(new Build());
 var point = new GLatLng(<?php echo $intLatitude ?>,<?php echo $intLongtitude ?>);
 map.addOverlay(createMarker(point, 0));
 

 map.addControl(new GSmallMapControl());
 map.enableDoubleClickZoom();
  }
}

//]]>
    </script>

    <style type="text/css">
      body {
        margin:0;
        border:0;
        padding:0;
      }
      div {
        margin:0;
        border:0;
        padding:0;
      }
    </style>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="width: 600px; height: 450px"></div>
  </body>
</html>