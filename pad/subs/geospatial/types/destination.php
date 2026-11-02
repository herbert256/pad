<?php
  return [ 'options' => [ 'unit', 'bearing' ], 'call' => function ( $a, $b, $options ) {
    [ $lon, $lat ] = array_map ( 'deg2rad', padGeoPoint ( $a ) );
    $bearing = $options ['bearing'] ?? NULL; $unit = $options ['unit'] ?? 'm';
    if ( ! is_numeric ( $b ) || ! is_finite ( (float) $b ) || $b < 0 || ! is_numeric ( $bearing ) || ! is_finite ( (float) $bearing ) )
      throw new InvalidArgumentException ( 'destination expects a nonnegative distance and finite bearing in degrees' );
    if ( ! is_string ( $unit ) || ! isset ( padUnits ( 'length' ) ['units'] [$unit] ) )
      throw new InvalidArgumentException ( 'destination unit must be a registered length symbol' );
    $distance = padConvert ( $b, $unit, 'm' );
    if ( $distance > M_PI * padGeoRadius ) throw new InvalidArgumentException ( 'destination distance exceeds half the spherical circumference' );
    if ( $distance == 0 ) return padGeoPoint ( $a );
    if ( abs ( $lat ) === M_PI / 2 ) throw new InvalidArgumentException ( 'bearing at a pole is undefined' );
    $theta = deg2rad ( fmod ( (float) $bearing, 360 ) ); $delta = $distance / padGeoRadius;
    $lat2 = asin ( max ( -1, min ( 1, sin ( $lat ) * cos ( $delta ) + cos ( $lat ) * sin ( $delta ) * cos ( $theta ) ) ) );
    $lon2 = $lon + atan2 ( sin ( $theta ) * sin ( $delta ) * cos ( $lat ), cos ( $delta ) - sin ( $lat ) * sin ( $lat2 ) );
    return [ padGeoLongitude ( rad2deg ( $lon2 ) ), rad2deg ( $lat2 ) ];
  } ];
?>
