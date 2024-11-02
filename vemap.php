<?php

$intLatitude=($_REQUEST['Latitude']);
$intLongtitude=($_REQUEST['Longtitude']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
   <head>
      <title></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1"></script>
      <script type="text/javascript">
         var map = null;
         
         function GetMap()
         {
            map = new VEMap('myMap');
            map.LoadMap(new VELatLong(<?php echo $intLatitude ?>, <?php echo $intLongtitude ?>), 12 ,'h' ,false);
         }   
      </script>
   </head>
   <body onload="GetMap();">
      <div id='myMap' style="position:relative; width:400px; height:400px;"></div>
   </body>
</html> 
