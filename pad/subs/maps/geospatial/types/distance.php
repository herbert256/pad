<?php
  return [ 'options' => [ 'unit' ], 'call' => function ( $a, $b, $options ) {
    [ $lon1, $lat1 ] = array_map ( 'deg2rad', padGeoPoint ( $a ) );
    [ $lon2, $lat2 ] = array_map ( 'deg2rad', padGeoPoint ( $b ) );
    $h = sin ( ( $lat2 - $lat1 ) / 2 ) ** 2 + cos ( $lat1 ) * cos ( $lat2 ) * sin ( ( $lon2 - $lon1 ) / 2 ) ** 2;
    $metres = 2 * padGeoRadius * atan2 ( sqrt ( min ( 1, max ( 0, $h ) ) ), sqrt ( max ( 0, 1 - $h ) ) );
    $unit = $options ['unit'] ?? 'm';
    if ( ! is_string ( $unit ) or ! isset ( padUnits ( 'length' ) ['units'] [$unit] ) )
      throw new InvalidArgumentException ( 'distance unit must be a registered length symbol' );
    return padConvert ( $metres, 'm', $unit );
  } ];
?>
