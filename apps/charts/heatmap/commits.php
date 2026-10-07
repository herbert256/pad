<?php

  // A year of commits: none on most weekends, more mid-week, a quiet August and a busy
  // run before the release in November - the same picture every time, from a fixed seed.

  mt_srand ( 2026 );

  $commits = [];

  for ( $day = new DateTimeImmutable ( '2026-01-01' ); $day->format ( 'Y' ) == '2026'; $day = $day->modify ( '+1 day' ) ) {

    $weekday = (int) $day->format ( 'N' );
    $month   = (int) $day->format ( 'n' );
    $chance  = ( $weekday >= 6 ) ? 0.12 : ( $month == 8 ? 0.25 : 0.8 );
    $busy    = ( $month == 11 ) ? 2.2 : ( in_array ( $weekday, [ 2, 3, 4 ] ) ? 1.3 : 1 );

    if ( mt_rand () / mt_getrandmax () < $chance )
      $commits [] = [ 'day' => $day->format ( 'Y-m-d' ), 'count' => (int) round ( mt_rand ( 1, 6 ) * $busy ) ];

  }

?>
