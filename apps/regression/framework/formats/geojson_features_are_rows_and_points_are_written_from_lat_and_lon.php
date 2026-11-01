<?php

  // GeoJSON: a feature a row - its properties, its id, the geometry's type, lat and lon of a
  // Point (written longitude first), the coordinates of any other, a feature without a
  // geometry '' - and rows with lat and lon written as Points, the other fields their
  // properties: the same features again.

  $geoText = '{"type":"FeatureCollection","features":['
           . '{"type":"Feature","id":"ams","geometry":{"type":"Point","coordinates":[4.9041,52.3676]},"properties":{"name":"Amsterdam","population":905234}},'
           . '{"type":"Feature","geometry":{"type":"LineString","coordinates":[[4.9,52.37],[4.35,50.85]]},"properties":{"name":"A road"}},'
           . '{"type":"Feature","geometry":null,"properties":{"name":"Nowhere"}}]}';

  $geoRows = padFormatRead ( 'geojson', $geoText );
  $geoOut  = padFormatWrite ( 'geojson', $geoRows );

  $geoJson = json_encode ( $geoRows );
  $geoShow = json_encode ( json_decode ( $geoOut ) );
  $geoSame = ( padFormatRead ( 'geojson', $geoOut ) == $geoRows ) ? 'read back the same' : 'read back otherwise';

  $geoNew  = json_encode ( json_decode ( padFormatWrite ( 'geojson', [ [ 'city' => 'Ghent', 'lat' => '51.0543', 'lng' => '3.7174' ] ] ) ) );

?>
