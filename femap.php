<?php

$intLatitude=($_REQUEST['Latitude']);
$intLongtitude=($_REQUEST['Longtitude']);

header("Location: http://www.flashearth.com/?lat=$intLatitude&lon=$intLongtitude&z=16&r=0&src=ggl");

?>
