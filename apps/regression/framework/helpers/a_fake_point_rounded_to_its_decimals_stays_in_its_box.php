<?php

  // padFake ( 'latitude' ) and ( 'longitude' ) rounded to fewer places than the box's edges
  // have stay in the box: decimals=0 in the Netherlands' box gives the latitudes 51 to 53
  // and the longitudes 4 to 7, every one of them; a box across the 180th meridian with a
  // single whole degree in it gives that degree, written 180 every time; one place from
  // -0.29 to 0.04 gives -0.2 to 0.0. The value was drawn in the box and then rounded, which
  // stepped past an edge: latitudes of 54 north of 53.55, longitudes of 3 west of 3.36, -0.3
  // south of -0.29, and 52.3 to 52.4 answered 52 every time (an error now, errors/syntax).

  padFakeSeed ( 4 );

  $rows = padFakeRows ( 'lat:latitude, lon:longitude', 2000, 5,
                        [ 'south' => 50.75, 'north' => 53.55, 'west' => 3.36, 'east' => 7.23, 'decimals' => 0 ] );

  $lats = array_unique ( array_column ( $rows, 'lat' ) );
  $lons = array_unique ( array_column ( $rows, 'lon' ) );

  sort ( $lats );
  sort ( $lons );

  $wrap = [];

  for ( $i = 0; $i < 200; $i++ )
    $wrap [ padFake ( 'longitude', [ 'west' => 179.5, 'east' => -179.6, 'decimals' => 0 ] ) ] = TRUE;

  $tenths = [];

  for ( $i = 0; $i < 200; $i++ )
    $tenths [ padFake ( 'latitude', [ 'south' => -0.29, 'north' => 0.04, 'decimals' => 1 ] ) ] = TRUE;

  ksort ( $tenths );

  $r = implode ( ',', $lats ) . ' | ' . implode ( ',', $lons ) . ' | ' . implode ( ',', array_keys ( $wrap ) ) . ' | ' . implode ( ',', array_keys ( $tenths ) );

?>
