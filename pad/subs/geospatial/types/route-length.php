<?php
  return [ 'options' => [ 'unit' ], 'call' => function ( $a, $b, $options ) {
    if ( $b !== NULL || ! is_array ( $a ) || ! array_is_list ( $a ) || count ( $a ) > 10000 )
      throw new InvalidArgumentException ( 'route-length expects a list of at most 10000 points and no second operand' );
    $points = array_map ( 'padGeoPoint', $a );
    $distance = include PAD . 'subs/geospatial/types/distance.php';
    // Validate options even when there are no legs.
    $distance ['call'] ( [0,0], [0,0], $options );
    $total = 0.0; $compensation = 0.0;
    for ( $i = 1; $i < count ( $points ); $i++ ) {
      $y = $distance ['call'] ( $points [$i - 1], $points [$i], $options ) - $compensation;
      $next = $total + $y; $compensation = ( $next - $total ) - $y; $total = $next;
    }
    return $total;
  } ];
?>
