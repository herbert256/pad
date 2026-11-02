<?php
  return [ 'options' => [ 'precision' ], 'call' => function ( $a, $b, $options ) {
    $alphabet = '0123456789bcdefghjkmnpqrstuvwxyz';
    $bounds = [ [ -180.0, 180.0 ], [ -90.0, 90.0 ] ];
    if ( is_string ( $a ) ) {
      if ( ! preg_match ( '/^[0-9bcdefghjkmnpqrstuvwxyz]{1,12}$/D', $a ) ) throw new InvalidArgumentException ( 'geohash is 1..12 lowercase base32 characters' );
      $bit = 0;
      foreach ( str_split ( $a ) as $character ) {
        $value = strpos ( $alphabet, $character );
        for ( $mask = 16; $mask; $mask >>= 1 ) {
          $axis = $bit++ % 2; $mid = array_sum ( $bounds [$axis] ) / 2;
          $bounds [$axis] [ ( $value & $mask ) ? 0 : 1 ] = $mid;
        }
      }
      return [ 'point' => [ array_sum ( $bounds [0] ) / 2, array_sum ( $bounds [1] ) / 2 ],
               'bbox' => [ $bounds [0][0], $bounds [1][0], $bounds [0][1], $bounds [1][1] ] ];
    }
    $point = padGeoPoint ( $a ); $precision = $options ['precision'] ?? 9;
    if ( ! is_int ( $precision ) or $precision < 1 or $precision > 12 ) throw new InvalidArgumentException ( 'geohash precision must be integer 1..12' );
    $text = ''; $value = 0;
    for ( $bit = 0; $bit < $precision * 5; $bit++ ) {
      $axis = $bit % 2; $mid = array_sum ( $bounds [$axis] ) / 2; $upper = $point [$axis] >= $mid;
      $bounds [$axis] [ $upper ? 0 : 1 ] = $mid; $value = ( $value << 1 ) | (int) $upper;
      if ( $bit % 5 === 4 ) { $text .= $alphabet [$value]; $value = 0; }
    }
    return $text;
  } ];
?>
