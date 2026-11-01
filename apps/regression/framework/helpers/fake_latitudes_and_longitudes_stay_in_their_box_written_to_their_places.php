<?php

  // padFake ( 'latitude' ) and ( 'longitude' ): every value inside its box and written with
  // its places, the zeros at the end kept and never as 1.0E-6 or -0; a box whose west lies
  // east of its east crosses the 180th meridian and fills both sides of it; the whole earth
  // without options; the same seed the same point.

  padFakeSeed ( 3 );

  $lats = $lons = $wraps = [];
  $shape = TRUE;

  for ( $i = 0; $i < 500; $i++ ) {

    $lat  = padFake ( 'latitude',  [ 'south' => 50.75, 'north' => 53.55 ] );
    $lon  = padFake ( 'longitude', [ 'west' => 3.36, 'east' => 7.23 ] );
    $wrap = padFake ( 'longitude', [ 'west' => 177, 'east' => -178, 'decimals' => 2 ] );
    $tiny = padFake ( 'latitude',  [ 'south' => -0.000001, 'north' => 0.000001 ] );

    $shape = ( $shape and preg_match ( '/^\d\d\.\d{6}$/D', $lat ) and preg_match ( '/^\d\.\d{6}$/D', $lon )
               and preg_match ( '/^-?\d{3}\.\d\d$/D', $wrap ) and preg_match ( '/^-?0\.00000[01]$/D', $tiny ) and $tiny !== '-0.000000' );

    $lats  [] = (float) $lat;
    $lons  [] = (float) $lon;
    $wraps [] = (float) $wrap;

  }

  $outside = array_filter ( $wraps, fn ( $one ) => $one < 177 and $one > -178 );
  $earth   = [];

  for ( $i = 0; $i < 200; $i++ )
    $earth [] = [ (float) padFake ( 'latitude' ), (float) padFake ( 'longitude' ) ];

  padFakeSeed ( 'x' );  $one = padFake ( 'latitude' ) . ',' . padFake ( 'longitude' );
  padFakeSeed ( 'x' );  $two = padFake ( 'latitude' ) . ',' . padFake ( 'longitude' );

  $r = json_encode ( [
    'shape'     => (bool) $shape,
    'latitude'  => min ( $lats ) >= 50.75 and max ( $lats ) <= 53.55 and min ( $lats ) < 51 and max ( $lats ) > 53.3,
    'longitude' => min ( $lons ) >= 3.36 and max ( $lons ) <= 7.23 and min ( $lons ) < 3.6 and max ( $lons ) > 7,
    'across'    => ! $outside and min ( $wraps ) >= -180 and max ( $wraps ) <= 180 and count ( array_filter ( $wraps, fn ( $one ) => $one < 0 ) ) > 100
                   and count ( array_filter ( $wraps, fn ( $one ) => $one > 0 ) ) > 200,
    'earth'     => min ( array_column ( $earth, 0 ) ) >= -90 and max ( array_column ( $earth, 0 ) ) <= 90
                   and min ( array_column ( $earth, 1 ) ) >= -180 and max ( array_column ( $earth, 1 ) ) <= 180
                   and min ( array_column ( $earth, 1 ) ) < -150 and max ( array_column ( $earth, 1 ) ) > 150,
    'places'    => [ preg_match ( '/^-?\d{1,2}$/D', padFake ( 'latitude', [ 'decimals' => '0' ] ) ),
                     strlen ( substr ( strrchr ( padFake ( 'longitude', [ 'decimals' => 10 ] ), '.' ), 1 ) ) ],
    'one'       => padFake ( 'latitude', [ 'south' => 12.5, 'north' => 12.5, 'decimals' => 3 ] ) . ' ' . padFake ( 'longitude', [ 'west' => 180, 'east' => 180, 'decimals' => 1 ] ),
    'seed'      => $one === $two,
  ] );

?>
