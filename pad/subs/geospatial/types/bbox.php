<?php
  return [ 'options' => [], 'call' => function ( $a, $b, $options ) {
    if ( ! is_array ( $a ) or ! $a or count ( $a ) > 10000 ) throw new InvalidArgumentException ( 'bbox expects 1..10000 points' );
    $points = array_map ( 'padGeoPoint', $a ); $longitudes = array_column ( $points, 0 ); sort ( $longitudes );
    $largest = -1; $start = 0; $end = 0; $n = count ( $longitudes );
    for ( $i = 0; $i < $n; $i++ ) {
      $gap = ( $i + 1 === $n ? $longitudes [0] + 360 : $longitudes [$i + 1] ) - $longitudes [$i];
      if ( $gap > $largest ) { $largest = $gap; $start = ( $i + 1 ) % $n; $end = $i; }
    }
    return [ $longitudes [$start], min ( array_column ( $points, 1 ) ), $longitudes [$end], max ( array_column ( $points, 1 ) ) ];
  } ];
?>
