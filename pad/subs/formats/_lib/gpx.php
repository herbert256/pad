<?php

  // GPX (GPS Exchange Format 1.1 - 1.0 is read as well), the reader and the writer of
  // types/gpx.php.
  //
  // The reader makes a row of every point, in the order of the document - the waypoints,
  // then the points of each route, then those of each track - all with the same fields:
  //
  //   type      waypoint, route or track
  //   track     the name of the route or track the point is on, '' for a waypoint
  //   segment   the number of the track segment, from 1 - 1 on a route, 0 for a waypoint
  //   point     the number of the point on its route or segment, from 1
  //   name, lat, lon, ele, time, desc
  //
  // lat, lon and ele are numbers; time is the text it is (2026-11-01T09:30:00Z).
  //
  // The writer groups rows the same way back: a row of type route goes in the route of its
  // track name, a row of type track in the track of its name and the segment of its number,
  // any other - and a row without a type - is a waypoint, unless as='track' or as='route'
  // makes the untyped rows one track or route. A row without lat and lon has no place on a
  // map and is left out. The document is ordered as GPX 1.1 has it: metadata, waypoints,
  // routes, tracks; name= names it in its metadata.

  function padGpxRead ( $text ) {

    $dom    = new DOMDocument ();
    $errors = libxml_use_internal_errors ( TRUE );

    try {
      $loaded = $dom->loadXML ( $text, LIBXML_NONET | LIBXML_NOCDATA );
      $error  = libxml_get_errors () [0] ?? NULL;
      libxml_clear_errors ();
    } finally {
      libxml_use_internal_errors ( $errors );
    }

    if ( ! $loaded )
      padFormatFail ( $error ? $error->line : 1, 'the XML cannot be read - ' . trim ( $error ? $error->message : 'no document' ) );

    $root = $dom->documentElement;

    if ( $root->localName != 'gpx' )
      padFormatFail ( $root->getLineNo (), "a GPX document is a <gpx> element, not <{$root->localName}>" );

    $rows = [];

    foreach ( padGpxChildren ( $root, 'wpt' ) as $wpt )
      $rows [] = padGpxPoint ( $wpt, 'waypoint', '', 0, 0 );

    foreach ( padGpxChildren ( $root, 'rte' ) as $rte ) {
      $name = padGpxText ( $rte, 'name' );
      foreach ( padGpxChildren ( $rte, 'rtept' ) as $index => $point )
        $rows [] = padGpxPoint ( $point, 'route', $name, 1, $index + 1 );
    }

    foreach ( padGpxChildren ( $root, 'trk' ) as $trk ) {
      $name = padGpxText ( $trk, 'name' );
      foreach ( padGpxChildren ( $trk, 'trkseg' ) as $segment => $seg )
        foreach ( padGpxChildren ( $seg, 'trkpt' ) as $index => $point )
          $rows [] = padGpxPoint ( $point, 'track', $name, $segment + 1, $index + 1 );
    }

    return $rows;

  }

  // The child elements of a name, whatever their namespace - GPX 1.0 and 1.1 have their own.

  function padGpxChildren ( $element, $name ) {

    $children = [];

    foreach ( $element->childNodes as $child )
      if ( $child instanceof DOMElement and $child->localName == $name )
        $children [] = $child;

    return $children;

  }

  function padGpxText ( $element, $name ) {

    return trim ( ( padGpxChildren ( $element, $name ) [0] ?? NULL )?->textContent ?? '' );

  }

  function padGpxPoint ( $point, $type, $track, $segment, $index ) {

    foreach ( [ 'lat', 'lon' ] as $axis )
      if ( ! is_numeric ( $point->getAttribute ( $axis ) ) )
        padFormatFail ( $point->getLineNo (), "a <{$point->localName}> has its $axis as a number" );

    $ele = padGpxText ( $point, 'ele' );

    return [
      'type'    => $type,
      'track'   => $track,
      'segment' => $segment,
      'point'   => $index,
      'name'    => padGpxText ( $point, 'name' ),
      'lat'     => (float) $point->getAttribute ( 'lat' ),
      'lon'     => (float) $point->getAttribute ( 'lon' ),
      'ele'     => is_numeric ( $ele ) ? (float) $ele : '',
      'time'    => padGpxText ( $point, 'time' ),
      'desc'    => padGpxText ( $point, 'desc' )
    ];

  }

  function padGpxWrite ( $rows, $options ) {

    $as        = in_array ( $options ['as'] ?? '', [ 'track', 'route' ], TRUE ) ? $options ['as'] : 'waypoint';
    $waypoints = [];
    $routes    = [];
    $tracks    = [];

    foreach ( $rows as $index => $row ) {

      $lat = padFormatPickValue ( $row, [ 'lat', 'latitude' ] );
      $lon = padFormatPickValue ( $row, [ 'lon', 'lng', 'long', 'longitude' ] );

      if ( ! is_numeric ( $lat ) or ! is_numeric ( $lon ) ) {
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' has no lat and lon to put on a map' );
        continue;
      }

      $type  = strtolower ( padFormatPick ( $row, [ 'type' ] ) );
      $type  = in_array ( $type, [ 'waypoint', 'route', 'track' ], TRUE ) ? $type : $as;
      $track = padFormatPick ( $row, [ 'track', 'route' ] ) ?: (string) ( $options ['name'] ?? '' );
      $point = padGpxPointXml ( $row, $lat, $lon, [ 'waypoint' => 'wpt', 'route' => 'rtept', 'track' => 'trkpt' ] [$type],
                                [ 'waypoint' => '  ', 'route' => '    ', 'track' => '      ' ] [$type] );

      if ( $type == 'waypoint' )
        $waypoints [] = $point;
      elseif ( $type == 'route' )
        $routes [$track] [] = $point;
      else
        $tracks [$track] [ max ( 1, (int) padFormatPick ( $row, [ 'segment' ] ) ) ] [] = $point;

    }

    $x   = 'padFormatXml';
    $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
         . '<gpx version="1.1" creator="' . $x ( $options ['creator'] ?? 'PAD' ) . "\" xmlns=\"http://www.topografix.com/GPX/1/1\">\n";

    if ( ( $options ['name'] ?? '' ) !== '' )
      $out .= '  <metadata>' . "\n" . '    <name>' . $x ( $options ['name'] ) . "</name>\n  </metadata>\n";

    $out .= implode ( '', $waypoints );

    foreach ( $routes as $name => $points )
      $out .= "  <rte>\n" . ( $name !== '' ? '    <name>' . $x ( $name ) . "</name>\n" : '' ) . implode ( '', $points ) . "  </rte>\n";

    foreach ( $tracks as $name => $segments ) {
      ksort ( $segments );
      $out .= "  <trk>\n" . ( $name !== '' ? '    <name>' . $x ( $name ) . "</name>\n" : '' );
      foreach ( $segments as $points )
        $out .= "    <trkseg>\n" . implode ( '', $points ) . "    </trkseg>\n";
      $out .= "  </trk>\n";
    }

    return $out . "</gpx>\n";

  }

  // A point: its position as attributes, then ele, time, name and desc in the order GPX 1.1
  // has them.

  function padGpxPointXml ( $row, $lat, $lon, $tag, $indent ) {

    $x     = 'padFormatXml';
    $inner = '';

    $ele = padFormatPickValue ( $row, [ 'ele', 'elevation', 'alt' ] );

    if ( is_numeric ( $ele ) )
      $inner .= "$indent  <ele>" . padFormatCell ( (float) $ele ) . "</ele>\n";

    $time = padFormatPickValue ( $row, [ 'time', 'date' ] );

    if ( $time !== NULL and ( $date = padFormatDate ( $time ) ) )
      $inner .= "$indent  <time>" . ( $date [1] && $date [2] === ''
                                      ? $date [0]->format ( 'Y-m-d\TH:i:s' ) . 'Z'
                                      : $date [0]->setTimezone ( new DateTimeZone ( 'UTC' ) )->format ( 'Y-m-d\TH:i:s\Z' ) ) . "</time>\n";

    foreach ( [ 'name' => [ 'name', 'title' ], 'desc' => [ 'desc', 'description', 'summary' ] ] as $element => $names )
      if ( ( $value = padFormatPick ( $row, $names ) ) !== '' )
        $inner .= "$indent  <$element>" . $x ( $value ) . "</$element>\n";

    $open = "$indent<$tag lat=\"" . padFormatCell ( (float) $lat ) . '" lon="' . padFormatCell ( (float) $lon ) . '"';

    return $inner === '' ? "$open/>\n" : "$open>\n$inner$indent</$tag>\n";

  }

?>
