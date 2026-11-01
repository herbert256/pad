<?php

  // gpx - GPS Exchange Format 1.1, read and written: every point a row - the waypoints, the
  // points of the routes, those of the track segments - with its type, track, segment,
  // point, name, lat, lon, ele, time and desc; rows as waypoints, routes and tracks again, by
  // their type (as='track' makes a list of positions one track). Not known on sight - a GPX
  // document is XML, and read as XML before: name it, format='gpx', or keep it in a .gpx file.

  return [
    'name'      => 'GPX',
    'mime'      => 'application/gpx+xml',
    'extension' => [ 'gpx' ],
    'shape'     => 'rows',
    'lib'       => 'gpx',
    'read'      => fn ( $text, $options ) => padGpxRead ( $text ),
    'write'     => fn ( $data, $options ) => padGpxWrite ( padFormatRows ( $data ), $options ),
    'download'  => 'attachment'
  ];

?>
