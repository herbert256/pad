<?php
  return [ 'options' => [], 'call' => function ( $a, $b, $options ) {
    [ $lon1, $lat1 ] = array_map ( 'deg2rad', padGeoPoint ( $a ) );
    [ $lon2, $lat2 ] = array_map ( 'deg2rad', padGeoPoint ( $b ) );
    $x = cos ( $lat1 ) * sin ( $lat2 ) - sin ( $lat1 ) * cos ( $lat2 ) * cos ( $lon2 - $lon1 );
    $y = sin ( $lon2 - $lon1 ) * cos ( $lat2 );
    if ( hypot ( $x, $y ) < 1e-14 or abs ( cos ( $lat1 ) ) < 1e-14 )
      throw new InvalidArgumentException ( 'bearing undefined at coincident/antipodal points or from a pole' );
    return fmod ( rad2deg ( atan2 ( $y, $x ) ) + 360, 360 );
  } ];
?>
