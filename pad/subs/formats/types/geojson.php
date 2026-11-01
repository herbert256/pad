<?php

  // geojson - GeoJSON (RFC 7946), read and written: a feature a row - its properties, its id,
  // geometry (the type), lat and lon of a Point, coordinates of any other - and rows as a
  // FeatureCollection, a Point for every row with lat and lon, the other fields its
  // properties. Not known on sight: a GeoJSON document is JSON, and read as JSON before -
  // name it, format='geojson', or keep it in a .geojson file.

  return [
    'name'      => 'GeoJSON',
    'mime'      => 'application/geo+json',
    'extension' => [ 'geojson' ],
    'shape'     => 'rows',
    'lib'       => 'geojson',
    'read'      => fn ( $text, $options ) => padGeoJsonRead ( $text ),
    'write'     => fn ( $data, $options ) => padGeoJsonWrite ( padFormatRows ( $data ) ),
    'download'  => 'inline'
  ];

?>
