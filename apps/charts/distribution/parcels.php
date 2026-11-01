<?php

  // How long 270 parcels took from the order to the door, in whole hours, by carrier - from a
  // fixed seed: Swift is quick and steady, Postal slower and steady, and Courier either
  // delivers the next day or misses the van and takes three. A few parcels never arrived:
  // their hours stay empty, and the chart counts them apart.

  mt_srand ( 23 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $parcels = [];

  for ( $i = 0; $i < 90; $i++ )
    $parcels [] = [ 'carrier' => 'Swift',   'hours' => max ( 6, round ( $bell ( 26, 5 ) ) ) ];

  for ( $i = 0; $i < 90; $i++ )
    $parcels [] = [ 'carrier' => 'Postal',  'hours' => ( $i % 45 == 44 ) ? '' : max ( 6, round ( $bell ( 44, 9 ) ) ) ];

  for ( $i = 0; $i < 90; $i++ )
    $parcels [] = [ 'carrier' => 'Courier', 'hours' => ( $i % 30 == 29 ) ? '' : max ( 6, round ( $i % 3 ? $bell ( 20, 3 ) : $bell ( 70, 6 ) ) ) ];

?>
