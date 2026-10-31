<?php

  // {map 'world', data='sales', key='country', value='amount'/} - a choropleth map as inline
  // SVG, drawn by the map subsystem in maps/ from Natural Earth's country outlines. The first
  // parameter is the view, a file of maps/types/ - world (the default), europe, africa,
  // benelux, middle-east ... - and the rest is the body every map runs, maps/map.php.

  $padMapView = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'maps/map.php';

?>
