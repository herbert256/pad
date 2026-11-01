<?php

  // kml - KML (OGC KML 2.2), read and written: a Placemark a row - its ExtendedData, id, name,
  // description, folder, address, style, time, and its geometry as GeoJSON's reader has it:
  // geometry, lat and lon of a Point, coordinates of any other (subs/formats/_lib/kml.php) -
  // and rows as a Document of placemarks, in folders by their folder, Points written from lat
  // and lon. Styles, overlays and a NetworkLink are no rows, the link never fetched; a
  // DOCTYPE is refused. Not known on sight - a KML document is XML, and read as XML before:
  // name it, format='kml', or keep it in a .kml file.

  return [
    'name'      => 'KML',
    'mime'      => 'application/vnd.google-earth.kml+xml',
    'extension' => [ 'kml' ],
    'shape'     => 'rows',
    'lib'       => 'kml',
    'read'      => fn ( $text, $options ) => padKmlRead ( $text ),
    'write'     => fn ( $data, $options ) => padKmlWrite ( padFormatRows ( $data ), $options ),
    'download'  => 'attachment'
  ];

?>
