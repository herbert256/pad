<?php
  return [ 'options' => [ 'unit' ], 'call' => function ( $a, $b, $options ) {
    $a = padGeoPoint ( $a );
    if ( ! is_array ( $b ) || count ( $b ) > 10000 ) throw new InvalidArgumentException ( 'nearest expects at most 10000 identity-keyed points' );
    $distance = include PAD . 'subs/geospatial/types/distance.php';
    $distance ['call'] ( $a, $a, $options ); $nearest = NULL;
    foreach ( $b as $id => $point ) {
      $point = padGeoPoint ( $point ); $length = $distance ['call'] ( $a, $point, $options );
      if ( $nearest === NULL || $length < $nearest ['distance'] )
        $nearest = [ 'id' => $id, 'point' => $point, 'distance' => $length, 'unit' => $options ['unit'] ?? 'm' ];
    }
    return $nearest;
  } ];
?>
