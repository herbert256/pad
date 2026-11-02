<?php

  // Rows written as KML: a Document named by name=, a Placemark a row in the order of the rows,
  // the rows of a folder - a / between nested ones - in their Folder where its first row
  // stands; name (or title), address, description (or summary), a TimeStamp of a date in UTC
  // or of a day, a TimeSpan of begin and end, styleUrl of style, an id that is an XML name
  // the Placemark's id and any other a Data, every other field a Data (a list as its JSON), the
  // text escaped; a Point of lat and lon (or latitude and lng) with ele its altitude, and a
  // geometry with the coordinates GeoJSON's reader gives - a Polygon closing its rings, a
  // MultiLineString and a GeometryCollection a MultiGeometry; a row without either has no
  // geometry. Read back, the rows are the same rows; the rows of a GeoJSON written as KML and
  // read again have the same geometries.

  $kmlRows = [
    [ 'id' => 'ghent', 'name' => 'Ghent <&>', 'lat' => 51.0543, 'lon' => 3.7174, 'ele' => 7, 'folder' => 'Belgium / Flanders',
      'time' => '2026-11-06T10:00:00+01:00', 'style' => '#red', 'tags' => [ 'old', 'river' ] ],
    [ 'id' => 2, 'title' => 'Brussels', 'latitude' => '50.8503', 'lng' => '4.3517', 'folder' => 'Belgium', 'begin' => '2026-11-07', 'end' => '2026-11-08' ],
    [ 'name' => 'Leie', 'geometry' => 'MultiLineString', 'coordinates' => [ [ [ 3.71, 51.05 ], [ 3.70, 51.045 ] ], [ [ 3.69, 51.04 ], [ 3.68, 51.03 ] ] ] ],
    [ 'name' => 'Square', 'address' => 'Korenmarkt, Ghent', 'summary' => 'A ring left open', 'geometry' => 'Polygon',
      'coordinates' => [ [ [ 3.72, 51.05 ], [ 3.73, 51.05 ], [ 3.73, 51.06 ] ] ], 'folder' => 'Belgium / Flanders' ],
    [ 'name' => 'Both', 'geometry' => 'GeometryCollection', 'geometries' => [ [ 'type' => 'Point', 'coordinates' => [ 4.35, 50.85 ] ],
                                                                           [ 'type' => 'LineString', 'coordinates' => [ [ 4.35, 50.85 ], [ 3.72, 51.05 ] ] ] ] ],
    [ 'name' => 'Nowhere', 'note' => 'no geometry' ]
  ];

  $kmlText = padFormatWrite ( 'kml', $kmlRows, [ 'name' => 'Places' ] );
  $kmlBack = padFormatRead ( 'kml', $kmlText );

  $kmlShow = preg_replace ( '/\n\s*/', '', $kmlText );
  $kmlSame = implode ( ' ', array_map ( fn ( $row ) => "{$row['name']}@{$row['folder']}={$row['geometry']}" . ( $row ['id'] ?? '' ), $kmlBack ) );

  $kmlGeo  = padFormatRead ( 'geojson', padFormatWrite ( 'geojson', $kmlBack ) );
  $kmlAgain = padFormatRead ( 'kml', padFormatWrite ( 'kml', $kmlGeo ) );
  $kmlGeoSame = ( array_column ( $kmlAgain, 'coordinates' ) == array_column ( $kmlBack, 'coordinates' )
                  and array_column ( $kmlAgain, 'lat' ) == array_column ( $kmlBack, 'lat' ) ) ? 'the same geometries through GeoJSON' : 'other geometries through GeoJSON';

?>
