<?php
  return [ 'options' => [ 'boundary' ], 'call' => function ( $a, $b, $options ) {
    $point = padGeoPoint ( $a ); $rings = padGeoPolygon ( $b );
    $boundary = $options ['boundary'] ?? TRUE;
    if ( ! is_bool ( $boundary ) ) throw new InvalidArgumentException ( 'boundary must be boolean' );
    $point [0] = $rings [0][0][0] + padGeoLongitude ( $point [0] - $rings [0][0][0] );
    $outer = padGeoInRing ( $point, $rings [0] );
    if ( $outer === 2 ) return $boundary;
    if ( ! $outer ) return FALSE;
    foreach ( array_slice ( $rings, 1 ) as $ring ) {
      $inside = padGeoInRing ( $point, $ring );
      if ( $inside === 2 ) return $boundary;
      if ( $inside ) return FALSE;
    }
    return TRUE;
  } ];
?>
