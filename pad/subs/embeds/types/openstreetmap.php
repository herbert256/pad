<?php

  // openstreetmap - A map of OpenStreetMap around a place, with a marker on it: named, the
  // place as 'latitude,longitude' - {embed:openstreetmap '52.3731,4.8922', zoom=15} - or lat= and
  // lon=; or an address of openstreetmap.org (or osm.org) that says it, #map=<zoom>/<lat>/
  // <lon> or ?mlat=&mlon=. zoom= is 1 (the world) to 19 (a street), 13 unless the address
  // says another. The poster is a drawn map: no tile is asked before the click.

  return [

    'name'    => 'OpenStreetMap',
    'kind'    => 'map',
    'frame'   => 'https://www.openstreetmap.org',
    'options' => [ 'lat', 'lon', 'zoom' ],

    'match'   => function ( $source, $named ) {

      if ( $named and $source === '' )
        return [];

      if ( $named and preg_match ( '/^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/D', $source, $m ) )
        return [ 'lat' => (float) $m [1], 'lon' => (float) $m [2] ];

      if ( ! $url = padEmbedUrl ( $source ) or ! in_array ( $url ['host'], [ 'openstreetmap.org', 'osm.org' ], TRUE ) )
        return NULL;

      $parts = [];

      if ( preg_match ( '#^(\d{1,2})/(-?\d{1,2}(?:\.\d+)?)/(-?\d{1,3}(?:\.\d+)?)$#D', (string) ( $url ['mark'] ['map'] ?? '' ), $m ) )
        $parts = [ 'zoom' => (int) $m [1], 'lat' => (float) $m [2], 'lon' => (float) $m [3] ];

      if ( is_numeric ( $url ['query'] ['mlat'] ?? NULL ) and is_numeric ( $url ['query'] ['mlon'] ?? NULL ) )
        $parts = [ 'lat' => (float) $url ['query'] ['mlat'], 'lon' => (float) $url ['query'] ['mlon'] ] + $parts;

      return $parts ? $parts : NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      global $padCheckSyntax;

      $lat  = $opts ['lat']  ?? $parts ['lat']  ?? NULL;
      $lon  = $opts ['lon']  ?? $parts ['lon']  ?? NULL;
      $zoom = $opts ['zoom'] ?? $parts ['zoom'] ?? 13;

      if ( ! is_numeric ( $lat ) or ! is_numeric ( $lon ) or abs ( $lat ) > 85 or abs ( $lon ) > 180 ) {
        if ( $padCheckSyntax )
          padError ( "the openstreetmap embed needs a place - 'latitude,longitude', lat= and lon=, or an openstreetmap.org address with #map= or mlat= and mlon=" );
        return [ 'src' => '' ];
      }

      if ( ! ctype_digit ( (string) $zoom ) or $zoom < 1 or $zoom > 19 ) {
        if ( $padCheckSyntax )
          padError ( "the openstreetmap embed has zoom='" . padMakeSafe ( (string) $zoom, 20 ) . "' - a whole number from 1 to 19" );
        $zoom = 13;
      }

      // The box the frame shows: a map of 720 by 405 pixels of 256-pixel tiles at the zoom,
      // around the place - narrower in latitude away from the equator, as the tiles are.

      $lat  = (float) $lat;
      $lon  = (float) $lon;
      $wide = 720 * 360 / ( 256 * 2 ** (int) $zoom ) / 2;
      $high = $wide * 405 / 720 * cos ( deg2rad ( $lat ) );
      $box  = implode ( ',', array_map ( fn ( $n ) => round ( $n, 5 ),
                [ max ( -180, $lon - $wide ), max ( -85, $lat - $high ), min ( 180, $lon + $wide ), min ( 85, $lat + $high ) ] ) );
      $at   = round ( $lat, 5 ) . ',' . round ( $lon, 5 );

      return [ 'src'  => 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode ( $box ) . '&layer=mapnik&marker=' . rawurlencode ( $at ),
               'link' => 'https://www.openstreetmap.org/?mlat=' . round ( $lat, 5 ) . '&mlon=' . round ( $lon, 5 ) . "#map=$zoom/" . round ( $lat, 5 ) . '/' . round ( $lon, 5 ) ];

    },

  ];

?>
