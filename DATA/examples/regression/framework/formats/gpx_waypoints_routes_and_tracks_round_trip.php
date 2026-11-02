<?php

  // GPX: every point a row - waypoints, then the points of each route and of each track
  // segment, numbered - with lat, lon and ele as numbers; written back in the order GPX 1.1
  // has, grouped by type, track and segment, the same rows when read again. as='track' makes
  // a list of positions one track.

  $gpxText = '<?xml version="1.0"?><gpx version="1.1" creator="x" xmlns="http://www.topografix.com/GPX/1/1">'
           . '<wpt lat="52.1" lon="4.2"><ele>3.5</ele><name>Start &amp; finish</name></wpt>'
           . '<rte><name>R</name><rtept lat="1" lon="2"/><rtept lat="3" lon="4"/></rte>'
           . '<trk><name>T</name><trkseg><trkpt lat="5" lon="6"><time>2026-11-01T09:30:00Z</time></trkpt></trkseg>'
           . '<trkseg><trkpt lat="7" lon="8"/></trkseg></trk></gpx>';

  $gpxRows = padFormatRead ( 'gpx', $gpxText );
  $gpxOut  = padFormatWrite ( 'gpx', $gpxRows, [ 'name' => 'Ride' ] );

  $gpxJson  = json_encode ( $gpxRows );
  $gpxShow  = str_replace ( "\n", '', $gpxOut );
  $gpxSame  = ( padFormatRead ( 'gpx', $gpxOut ) == $gpxRows ) ? 'read back the same' : 'read back otherwise';
  $gpxTrack = str_replace ( "\n", '', padFormatWrite ( 'gpx', [ [ 'lat' => 1, 'lon' => 2 ], [ 'lat' => 3, 'lon' => 4 ] ], [ 'as' => 'track' ] ) );

?>
