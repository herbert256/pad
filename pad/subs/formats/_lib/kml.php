<?php

  // KML (OGC KML 2.2, OGC 07-147r2 - 2.0, 2.1 and 2.3 are read as well), the reader and the
  // writer of types/kml.php, for the part of KML that is data: the placemarks.
  //
  // The reader makes a row of every Placemark, in the order of the document, whatever Document
  // or Folder it stands in - the fields of its ExtendedData (a Data's value, a SimpleData of
  // SchemaData) first, then:
  //
  //   id           the Placemark's id, when it has one
  //   name, description, folder
  //                its name, its description - the balloon's HTML, as KML has it - and the
  //                names of the folders it stands in, a / between them ('' for none)
  //   address, style, time, begin, end
  //                its address, its styleUrl (#red-pin), the when of its TimeStamp and the
  //                begin and end of its TimeSpan - each only when it has one
  //   geometry, lat, lon, ele, coordinates, geometries
  //                as the GeoJSON reader has them, so a row of one is written as the other:
  //                lat and lon (and ele, an altitude) of a Point, the coordinates - [ lon, lat,
  //                alt ] positions - of a LineString, a Polygon (its outer ring, then its inner
  //                ones, a ring not closed closed), a MultiGeometry of one kind (MultiPoint,
  //                MultiLineString, MultiPolygon) and a gx:Track; a MultiGeometry of mixed
  //                kinds is a GeometryCollection with its geometries. A LinearRing on its own
  //                is a LineString. A placemark without a geometry, or with a Model, has none.
  //
  // What is not a row is passed over: styles, a NetworkLink - never fetched - and the
  // overlays. The document is read with no network and no DOCTYPE: a DOCTYPE, which could
  // declare entities, is refused. A text that is no XML, a root that is no <kml>, a
  // <coordinates> that holds no positions - lon,lat or lon,lat,alt, with a longitude of -180
  // to 180 and a latitude of -90 to 90 - and a geometry without the positions it needs are
  // the format's error, naming the line.
  //
  // The writer makes a Document of rows - name= names it - a Placemark a row: name (or title),
  // address, description (or desc, summary), a TimeStamp of time (or when, date) or a
  // TimeSpan of begin (or an event's start) and end, styleUrl of style, every other field a
  // Data of its ExtendedData - an empty name, description or other field of the placemark's
  // own none - and a Point of lat and lon (ele its altitude), or the geometry and coordinates
  // the reader gives; a row without either has no geometry, as in GeoJSON, and one whose
  // geometry has no positions none either, an error under the strict check. The rows of a
  // folder= - a / between nested ones - go in their Folder, which stands where its first row
  // stands. An id that is an XML name is the Placemark's id, any other a Data. The elements
  // stand in the order the KML 2.2 schema has them.

  function padKmlRead ( $text ) {

    if ( preg_match ( '/<!DOCTYPE/i', $text, $m, PREG_OFFSET_CAPTURE ) )
      padFormatFail ( padFormatLineAt ( $text, $m [0] [1] ), 'a KML document has no DOCTYPE - its entities are never read' );

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

    if ( $root->localName != 'kml' )
      padFormatFail ( $root->getLineNo (), "a KML document is a <kml> element, not <{$root->localName}>" );

    $rows = [];

    padKmlFeatures ( $root, [], $rows );

    return $rows;

  }

  // The placemarks of a container, those of its Documents and Folders in their turn.

  function padKmlFeatures ( $element, $folders, &$rows ) {

    foreach ( padKmlChildren ( $element ) as $child )
      if ( $child->localName == 'Placemark' )
        $rows [] = padKmlPlacemark ( $child, $folders );
      elseif ( $child->localName == 'Document' )
        padKmlFeatures ( $child, $folders, $rows );
      elseif ( $child->localName == 'Folder' )
        padKmlFeatures ( $child, array_merge ( $folders, [ padKmlText ( $child, 'name' ) ] ), $rows );

  }

  function padKmlChildren ( $element, $name = '' ) {

    $children = [];

    foreach ( $element->childNodes as $child )
      if ( $child instanceof DOMElement and ( $name === '' or $child->localName == $name ) )
        $children [] = $child;

    return $children;

  }

  function padKmlChild ( $element, $name ) {

    return padKmlChildren ( $element, $name ) [0] ?? NULL;

  }

  function padKmlText ( $element, $name ) {

    return trim ( padKmlChild ( $element, $name )?->textContent ?? '' );

  }

  function padKmlPlacemark ( $placemark, $folders ) {

    $row = [];

    if ( $data = padKmlChild ( $placemark, 'ExtendedData' ) ) {
      foreach ( padKmlChildren ( $data, 'Data' ) as $one )
        if ( $one->getAttribute ( 'name' ) !== '' )
          $row [ $one->getAttribute ( 'name' ) ] = padKmlText ( $one, 'value' );
      foreach ( padKmlChildren ( $data, 'SchemaData' ) as $schema )
        foreach ( padKmlChildren ( $schema, 'SimpleData' ) as $one )
          if ( $one->getAttribute ( 'name' ) !== '' )
            $row [ $one->getAttribute ( 'name' ) ] = trim ( $one->textContent );
    }

    if ( $placemark->hasAttribute ( 'id' ) )
      $row ['id'] = $placemark->getAttribute ( 'id' );

    $row ['name']        = padKmlText ( $placemark, 'name' );
    $row ['description'] = padKmlText ( $placemark, 'description' );
    $row ['folder']      = implode ( ' / ', $folders );

    if ( padKmlChild ( $placemark, 'address' ) )
      $row ['address'] = padKmlText ( $placemark, 'address' );

    if ( padKmlChild ( $placemark, 'styleUrl' ) )
      $row ['style'] = padKmlText ( $placemark, 'styleUrl' );

    if ( $stamp = padKmlChild ( $placemark, 'TimeStamp' ) )
      $row ['time'] = padKmlText ( $stamp, 'when' );

    if ( $span = padKmlChild ( $placemark, 'TimeSpan' ) ) {
      $row ['begin'] = padKmlText ( $span, 'begin' );
      $row ['end']   = padKmlText ( $span, 'end' );
    }

    $row ['geometry'] = '';
    $row ['lat']      = '';
    $row ['lon']      = '';

    foreach ( padKmlChildren ( $placemark ) as $child )
      if ( $geometry = padKmlGeometry ( $child ) ) {
        $row ['geometry'] = $geometry ['type'];
        if ( $geometry ['type'] == 'Point' ) {
          $row ['lon'] = $geometry ['coordinates'] [0];
          $row ['lat'] = $geometry ['coordinates'] [1];
          if ( isset ( $geometry ['coordinates'] [2] ) )
            $row ['ele'] = $geometry ['coordinates'] [2];
        } elseif ( $geometry ['type'] == 'GeometryCollection' )
          $row ['geometries'] = $geometry ['geometries'];
        else
          $row ['coordinates'] = $geometry ['coordinates'];
        break;
      }

    return $row;

  }

  // A geometry element as a GeoJSON geometry - type and coordinates, or geometries - or NULL
  // for an element that is none.

  function padKmlGeometry ( $element ) {

    switch ( $element->localName ) {

      case 'Point':
        $positions = padKmlCoordinates ( $element );
        if ( count ( $positions ) != 1 )
          padFormatFail ( $element->getLineNo (), 'a <Point> has one position in its <coordinates>, not ' . count ( $positions ) );
        return [ 'type' => 'Point', 'coordinates' => $positions [0] ];

      case 'LineString':
      case 'LinearRing':
        $positions = padKmlCoordinates ( $element );
        if ( count ( $positions ) < 2 )
          padFormatFail ( $element->getLineNo (), "a <{$element->localName}> has two positions or more in its <coordinates>" );
        return [ 'type' => 'LineString', 'coordinates' => $positions ];

      case 'Polygon':
        $outer = padKmlChild ( $element, 'outerBoundaryIs' );
        if ( ! $outer or ! padKmlChild ( $outer, 'LinearRing' ) )
          padFormatFail ( $element->getLineNo (), 'a <Polygon> has an <outerBoundaryIs> with its <LinearRing>' );
        $rings = [ padKmlRing ( padKmlChild ( $outer, 'LinearRing' ) ) ];
        foreach ( padKmlChildren ( $element, 'innerBoundaryIs' ) as $inner )
          foreach ( padKmlChildren ( $inner, 'LinearRing' ) as $ring )
            $rings [] = padKmlRing ( $ring );
        return [ 'type' => 'Polygon', 'coordinates' => $rings ];

      case 'Track':
        $positions = [];
        foreach ( padKmlChildren ( $element, 'coord' ) as $coord )
          $positions [] = padKmlPosition ( preg_split ( '/\s+/', trim ( $coord->textContent ) ), $coord );
        if ( count ( $positions ) < 2 )
          padFormatFail ( $element->getLineNo (), 'a <gx:Track> has two <gx:coord> or more' );
        return [ 'type' => 'LineString', 'coordinates' => $positions ];

      case 'MultiGeometry':
      case 'MultiTrack':
        $parts = [];
        foreach ( padKmlChildren ( $element ) as $child )
          if ( $part = padKmlGeometry ( $child ) )
            $parts [] = $part;
        if ( ! $parts )
          return NULL;
        $kinds = array_values ( array_unique ( array_column ( $parts, 'type' ) ) );
        if ( count ( $kinds ) == 1 and in_array ( $kinds [0], [ 'Point', 'LineString', 'Polygon' ], TRUE ) )
          return [ 'type' => 'Multi' . $kinds [0], 'coordinates' => array_column ( $parts, 'coordinates' ) ];
        return [ 'type' => 'GeometryCollection', 'geometries' => $parts ];

    }

    return NULL;

  }

  // A ring of a polygon: three positions at least, closed when its last is not its first.

  function padKmlRing ( $ring ) {

    $positions = padKmlCoordinates ( $ring );

    if ( count ( $positions ) < 3 )
      padFormatFail ( $ring->getLineNo (), 'a <LinearRing> of a polygon has three positions or more in its <coordinates>' );

    if ( $positions [0] != end ( $positions ) )
      $positions [] = $positions [0];

    return $positions;

  }

  // The positions of the <coordinates> of an element: lon,lat or lon,lat,alt, apart by white
  // space - a space after a comma read as none, as Google Earth reads it.

  function padKmlCoordinates ( $element ) {

    $coordinates = padKmlChild ( $element, 'coordinates' );

    if ( ! $coordinates )
      padFormatFail ( $element->getLineNo (), "a <{$element->localName}> has its <coordinates>" );

    $text      = trim ( preg_replace ( '/\s*,\s*/', ',', $coordinates->textContent ) );
    $positions = [];

    foreach ( $text === '' ? [] : preg_split ( '/\s+/', $text ) as $tuple )
      $positions [] = padKmlPosition ( explode ( ',', $tuple ), $coordinates );

    return $positions;

  }

  function padKmlPosition ( $parts, $element ) {

    if ( count ( $parts ) < 2 or count ( $parts ) > 3 or count ( array_filter ( $parts, 'is_numeric' ) ) != count ( $parts ) )
      padFormatFail ( $element->getLineNo (), "a position is lon,lat or lon,lat,alt - '" . padMakeSafe ( implode ( ',', $parts ), 40 ) . "' is none" );

    $position = array_map ( fn ( $one ) => (float) $one, $parts );

    if ( abs ( $position [0] ) > 180 or abs ( $position [1] ) > 90 )
      padFormatFail ( $element->getLineNo (), 'a position has a longitude of -180 to 180 and a latitude of -90 to 90 - '
                                              . padMakeSafe ( implode ( ',', $parts ), 40 ) . ' has not' );

    return $position;

  }

  // The folders are a tree, a / between the names of a folder= path, each standing where its
  // first row stands: rows read from KML are written back in their order and folders.

  function padKmlWrite ( $rows, $options ) {

    $tree = [ 'items' => [], 'folders' => [] ];

    foreach ( $rows as $index => $row ) {
      $node = &$tree;
      foreach ( preg_split ( '#\s*/\s*#', trim ( padFormatPick ( $row, [ 'folder' ] ) ), -1, PREG_SPLIT_NO_EMPTY ) as $name ) {
        if ( ! isset ( $node ['folders'] [$name] ) ) {
          $node ['folders'] [$name] = [ 'items' => [], 'folders' => [] ];
          $node ['items'] [] = [ 'folder', $name ];
        }
        $node = &$node ['folders'] [$name];
      }
      $node ['items'] [] = [ 'row', $index ];
      unset ( $node );
    }

    $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<kml xmlns=\"http://www.opengis.net/kml/2.2\">\n  <Document>\n";

    if ( ( $options ['name'] ?? '' ) !== '' )
      $out .= '    <name>' . padFormatXml ( $options ['name'] ) . "</name>\n";

    return $out . padKmlFolderXml ( $tree, $rows, '    ' ) . "  </Document>\n</kml>\n";

  }

  function padKmlFolderXml ( $node, $rows, $indent ) {

    $out = '';

    foreach ( $node ['items'] as [ $kind, $item ] )
      $out .= ( $kind == 'row' )
            ? padKmlPlacemarkXml ( $rows [$item], $item, $indent )
            : "$indent<Folder>\n$indent  <name>" . padFormatXml ( $item ) . "</name>\n"
              . padKmlFolderXml ( $node ['folders'] [$item], $rows, "$indent  " ) . "$indent</Folder>\n";

    return $out;

  }

  function padKmlPlacemarkXml ( $row, $index, $indent ) {

    $x    = 'padFormatXml';
    $used = [ 'folder', 'geometry', 'coordinates', 'geometries', 'lat', 'latitude', 'lon', 'lng', 'long', 'longitude', 'ele', 'elevation', 'alt' ];
    $pick = function ( $names ) use ( $row, &$used ) {
      foreach ( $names as $name )
        if ( padFormatPick ( $row, [ $name ] ) !== '' ) {
          $used [] = $name;
          return padFormatPick ( $row, [ $name ] );
        }
      return '';
    };

    $id    = padFormatPick ( $row, [ 'id' ] );
    $attr  = '';

    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9._-]*$/D', $id ) ) {
      $attr   = ' id="' . $x ( $id ) . '"';
      $used [] = 'id';
    }

    $inner = '';

    foreach ( [ 'name' => [ 'name', 'title' ], 'address' => [ 'address' ], 'description' => [ 'description', 'desc', 'summary' ] ] as $element => $names )
      if ( ( $value = $pick ( $names ) ) !== '' )
        $inner .= "$indent  <$element>" . $x ( $value ) . "</$element>\n";

    $time = padFormatPickValue ( $row, [ 'time', 'when', 'date' ] );

    if ( $time !== NULL and ( $when = padKmlWhen ( $time ) ) !== '' ) {
      $inner .= "$indent  <TimeStamp>\n$indent    <when>$when</when>\n$indent  </TimeStamp>\n";
      array_push ( $used, 'time', 'when', 'date' );
    } else {
      $begin = padKmlWhen ( padFormatPickValue ( $row, [ 'begin', 'start' ] ) );
      $end   = padKmlWhen ( padFormatPickValue ( $row, [ 'end' ] ) );
      if ( $begin !== '' or $end !== '' ) {
        $inner .= "$indent  <TimeSpan>\n" . ( $begin !== '' ? "$indent    <begin>$begin</begin>\n" : '' )
                . ( $end !== '' ? "$indent    <end>$end</end>\n" : '' ) . "$indent  </TimeSpan>\n";
        array_push ( $used, 'begin', 'start', 'end' );
      }
    }

    if ( ( $style = $pick ( [ 'style', 'styleUrl' ] ) ) !== '' )
      $inner .= "$indent  <styleUrl>" . $x ( $style ) . "</styleUrl>\n";

    // The other fields are Data - but a field of the placemark's own that is empty, the
    // description '' a row read from KML has, is none.

    $own  = [ 'id', 'name', 'title', 'address', 'description', 'desc', 'summary', 'time', 'when', 'date', 'begin', 'start', 'end', 'style', 'styleUrl' ];
    $data = '';

    foreach ( $row as $name => $value )
      if ( ! in_array ( (string) $name, $used, TRUE ) and $value !== NULL and ! ( is_object ( $value ) and ! $value instanceof Stringable and ! $value instanceof DateTimeInterface )
           and ! ( in_array ( (string) $name, $own, TRUE ) and is_scalar ( $value ) and (string) $value === '' ) )
        $data .= "$indent    <Data name=\"" . $x ( $name ) . "\">\n$indent      <value>"
               . $x ( $value instanceof DateTimeInterface ? $value->format ( 'Y-m-d\TH:i:sP' ) : padFormatCell ( $value ) ) . "</value>\n$indent    </Data>\n";

    if ( $data !== '' )
      $inner .= "$indent  <ExtendedData>\n$data$indent  </ExtendedData>\n";

    $inner .= padKmlGeometryXml ( padKmlRowGeometry ( $row, $index ), "$indent  " );

    return "$indent<Placemark$attr>\n$inner$indent</Placemark>\n";

  }

  // A date of KML: the day, or the moment - in UTC, or local as it is written without a zone.

  function padKmlWhen ( $value ) {

    if ( $value === NULL or ! ( $date = padFormatDate ( $value ) ) )
      return '';

    if ( ! $date [1] )
      return $date [0]->format ( 'Y-m-d' );

    return ( $date [2] === '' ) ? $date [0]->format ( 'Y-m-d\TH:i:s' ) : $date [0]->setTimezone ( new DateTimeZone ( 'UTC' ) )->format ( 'Y-m-d\TH:i:s\Z' );

  }

  // The geometry of a row, as GeoJSON has it - or NULL.

  function padKmlRowGeometry ( $row, $index ) {

    $lat = padFormatPickValue ( $row, [ 'lat', 'latitude' ] );
    $lon = padFormatPickValue ( $row, [ 'lon', 'lng', 'long', 'longitude' ] );

    if ( is_numeric ( $lat ) and is_numeric ( $lon ) ) {
      $point = [ (float) $lon, (float) $lat ];
      if ( is_numeric ( $ele = padFormatPickValue ( $row, [ 'ele', 'elevation', 'alt' ] ) ) )
        $point [] = (float) $ele;
      $geometry = [ 'type' => 'Point', 'coordinates' => $point ];
    } elseif ( is_array ( $row ['geometry'] ?? NULL ) )
      $geometry = $row ['geometry'];
    elseif ( is_string ( $row ['geometry'] ?? NULL ) and $row ['geometry'] !== '' )
      $geometry = ( $row ['geometry'] == 'GeometryCollection' )
                ? [ 'type' => 'GeometryCollection', 'geometries' => $row ['geometries'] ?? [] ]
                : [ 'type' => $row ['geometry'], 'coordinates' => $row ['coordinates'] ?? NULL ];
    else
      return NULL;

    if ( ! padKmlValid ( $geometry ) ) {
      padFormatSkip ( 'row ' . ( $index + 1 ) . ' has a geometry that is none - lat and lon, or a GeoJSON geometry and its coordinates' );
      return NULL;
    }

    return $geometry;

  }

  function padKmlValid ( $geometry, $depth = 0 ) {

    $position = fn ( $p ) => is_array ( $p ) and array_is_list ( $p ) and count ( $p ) >= 2 and count ( $p ) <= 3
                             and count ( array_filter ( $p, fn ( $n ) => is_int ( $n ) or is_float ( $n ) or ( is_string ( $n ) and is_numeric ( $n ) ) ) ) == count ( $p );
    $list     = fn ( $l, $test, $min ) => is_array ( $l ) and array_is_list ( $l ) and count ( $l ) >= $min and count ( array_filter ( $l, $test ) ) == count ( $l );
    $line     = fn ( $l ) => $list ( $l, $position, 2 );
    $polygon  = fn ( $l ) => $list ( $l, fn ( $r ) => $list ( $r, $position, 3 ), 1 );

    if ( ! is_array ( $geometry ) or $depth > 8 )
      return FALSE;

    $c = $geometry ['coordinates'] ?? NULL;

    return match ( $geometry ['type'] ?? '' ) {
      'Point'              => $position ( $c ),
      'LineString'         => $line ( $c ),
      'Polygon'            => $polygon ( $c ),
      'MultiPoint'         => $list ( $c, $position, 1 ),
      'MultiLineString'    => $list ( $c, $line, 1 ),
      'MultiPolygon'       => $list ( $c, $polygon, 1 ),
      'GeometryCollection' => $list ( $geometry ['geometries'] ?? NULL, fn ( $g ) => padKmlValid ( $g, $depth + 1 ), 1 ),
      default              => FALSE
    };

  }

  function padKmlGeometryXml ( $geometry, $indent ) {

    if ( $geometry === NULL )
      return '';

    $c      = $geometry ['coordinates'] ?? [];
    $tuples = fn ( $positions ) => implode ( ' ', array_map ( fn ( $p ) => implode ( ',', array_map ( fn ( $n ) => padFormatCell ( (float) $n ), $p ) ), $positions ) );
    $coords = fn ( $positions, $in ) => "$in<coordinates>" . $tuples ( $positions ) . "</coordinates>\n";
    $ring   = fn ( $positions, $in ) => "$in<LinearRing>\n" . $coords ( $positions [0] != end ( $positions ) ? [ ...$positions, $positions [0] ] : $positions, "$in  " ) . "$in</LinearRing>\n";

    switch ( $geometry ['type'] ) {

      case 'Point':
        return "$indent<Point>\n" . $coords ( [ $c ], "$indent  " ) . "$indent</Point>\n";

      case 'LineString':
        return "$indent<LineString>\n" . $coords ( $c, "$indent  " ) . "$indent</LineString>\n";

      case 'Polygon':
        $out = "$indent<Polygon>\n$indent  <outerBoundaryIs>\n" . $ring ( $c [0], "$indent    " ) . "$indent  </outerBoundaryIs>\n";
        foreach ( array_slice ( $c, 1 ) as $inner )
          $out .= "$indent  <innerBoundaryIs>\n" . $ring ( $inner, "$indent    " ) . "$indent  </innerBoundaryIs>\n";
        return $out . "$indent</Polygon>\n";

    }

    $parts = ( $geometry ['type'] == 'GeometryCollection' )
           ? $geometry ['geometries']
           : array_map ( fn ( $one ) => [ 'type' => substr ( $geometry ['type'], 5 ), 'coordinates' => $one ], $c );

    $out = "$indent<MultiGeometry>\n";

    foreach ( $parts as $part )
      $out .= padKmlGeometryXml ( $part, "$indent  " );

    return $out . "$indent</MultiGeometry>\n";

  }

?>
