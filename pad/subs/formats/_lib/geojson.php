<?php

  // GeoJSON (RFC 7946), the reader and the writer of types/geojson.php.
  //
  // The reader takes a FeatureCollection - or one Feature, or a bare geometry - and makes a
  // row of each feature: its properties as they are, then id when the feature has one,
  // geometry (the geometry's type, '' for none), and lat and lon for a Point - GeoJSON
  // writes longitude first, the row has them by name - and coordinates, the geometry's
  // own list, for any other. A GeometryCollection's members are in geometries.
  //
  // The writer makes a FeatureCollection of rows: a row with lat and lon - latitude,
  // longitude or lng read too - is a Point; one with a geometry name and coordinates, as the
  // reader gives them, is that geometry again; any other has none (null). Every other field
  // is a property; id stays the feature's id.

  function padGeoJsonRead ( $text ) {

    $doc = padFormatJson ( $text );

    if ( ! is_array ( $doc ) or ! isset ( $doc ['type'] ) or ! is_string ( $doc ['type'] ) )
      padFormatFail ( 1, 'a GeoJSON document is an object with a type - FeatureCollection, Feature or a geometry' );

    if ( $doc ['type'] == 'FeatureCollection' ) {

      if ( ! is_array ( $doc ['features'] ?? NULL ) or ! array_is_list ( $doc ['features'] ) )
        padFormatFail ( padGeoJsonLine ( $text, '"features"' ), 'a FeatureCollection has a list of features' );

      $features = $doc ['features'];

    } elseif ( $doc ['type'] == 'Feature' )
      $features = [ $doc ];
    elseif ( padGeoJsonGeometry ( $doc ['type'] ) )
      $features = [ [ 'type' => 'Feature', 'geometry' => $doc, 'properties' => NULL ] ];
    else
      padFormatFail ( padGeoJsonLine ( $text, '"type"' ), "'" . padMakeSafe ( $doc ['type'], 30 ) . "' is no GeoJSON type - FeatureCollection, Feature, Point, LineString, Polygon, Multi... or GeometryCollection" );

    $rows = [];

    foreach ( $features as $index => $feature ) {

      if ( ! is_array ( $feature ) or ( $feature ['type'] ?? '' ) != 'Feature' )
        padFormatFail ( padGeoJsonLine ( $text, '"type"', $index + 1 ), 'feature ' . ( $index + 1 ) . ' is no Feature' );

      $row = is_array ( $feature ['properties'] ?? NULL ) ? $feature ['properties'] : [];

      if ( array_key_exists ( 'id', $feature ) )
        $row ['id'] = $feature ['id'];

      $geometry = $feature ['geometry'] ?? NULL;

      if ( is_array ( $geometry ) and ! is_string ( $geometry ['type'] ?? '' ) )
        padFormatFail ( padGeoJsonLine ( $text, '"geometry"', $index + 1 ), 'feature ' . ( $index + 1 ) . ' has a geometry whose type is no text' );

      $row ['geometry'] = is_array ( $geometry ) ? (string) ( $geometry ['type'] ?? '' ) : '';
      $row ['lat']      = '';
      $row ['lon']      = '';

      if ( $row ['geometry'] == 'Point' and isset ( $geometry ['coordinates'] [0], $geometry ['coordinates'] [1] ) ) {
        $row ['lon'] = $geometry ['coordinates'] [0];
        $row ['lat'] = $geometry ['coordinates'] [1];
        if ( isset ( $geometry ['coordinates'] [2] ) )
          $row ['ele'] = $geometry ['coordinates'] [2];
      } elseif ( $row ['geometry'] == 'GeometryCollection' )
        $row ['geometries'] = $geometry ['geometries'] ?? [];
      elseif ( $row ['geometry'] !== '' )
        $row ['coordinates'] = $geometry ['coordinates'] ?? [];

      $rows [] = $row;

    }

    return $rows;

  }

  function padGeoJsonGeometry ( $type ) {

    return in_array ( $type, [ 'Point', 'MultiPoint', 'LineString', 'MultiLineString', 'Polygon', 'MultiPolygon', 'GeometryCollection' ], TRUE );

  }

  // The line of the $nth occurrence of a key in the text - where a fault of the document's
  // shape stands, as near as a decoded value can say.

  function padGeoJsonLine ( $text, $key, $nth = 1 ) {

    $at = -1;

    for ( $i = 0; $i < $nth; $i++ )
      if ( ( $at = strpos ( $text, $key, $at + 1 ) ) === FALSE )
        return 1;

    return padFormatLineAt ( $text, $at );

  }

  function padGeoJsonWrite ( $rows ) {

    $features = [];

    foreach ( $rows as $row ) {

      $lat = padFormatPickValue ( $row, [ 'lat', 'latitude' ] );
      $lon = padFormatPickValue ( $row, [ 'lon', 'lng', 'long', 'longitude' ] );

      if ( is_numeric ( $lat ) and is_numeric ( $lon ) ) {
        $point = [ (float) $lon, (float) $lat ];
        if ( is_numeric ( $row ['ele'] ?? NULL ) )
          $point [] = (float) $row ['ele'];
        $geometry = [ 'type' => 'Point', 'coordinates' => $point ];
      } elseif ( is_string ( $row ['geometry'] ?? NULL ) and padGeoJsonGeometry ( $row ['geometry'] ) and $row ['geometry'] != 'Point' )
        $geometry = ( $row ['geometry'] == 'GeometryCollection' )
                  ? [ 'type' => 'GeometryCollection', 'geometries' => $row ['geometries'] ?? [] ]
                  : [ 'type' => $row ['geometry'], 'coordinates' => $row ['coordinates'] ?? [] ];
      elseif ( is_array ( $row ['geometry'] ?? NULL ) and isset ( $row ['geometry'] ['type'] ) )
        $geometry = $row ['geometry'];
      else
        $geometry = NULL;

      $feature = [ 'type' => 'Feature' ];

      if ( array_key_exists ( 'id', $row ) and ( is_string ( $row ['id'] ) or is_int ( $row ['id'] ) or is_float ( $row ['id'] ) ) )
        $feature ['id'] = $row ['id'];

      $feature ['geometry']   = $geometry;
      $feature ['properties'] = (object) array_diff_key ( $row, array_flip ( [ 'id', 'lat', 'latitude', 'lon', 'lng', 'long', 'longitude',
                                                                              'ele', 'geometry', 'coordinates', 'geometries' ] ) );

      $features [] = $feature;

    }

    return json_encode ( padJsonFinite ( [ 'type' => 'FeatureCollection', 'features' => $features ] ),
                         JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR
                         | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n";

  }

?>
