<?php

  // 3,000 taxi rides through a city, made from a fixed seed: the distance in kilometres and
  // the fare in euros - a start fee and a price a kilometre, more in traffic - and a band of
  // rides to the airport, 18 to 24 kilometres for a fixed fare of 55.

  mt_srand ( 31 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $rides = [];

  for ( $i = 0; $i < 3000; $i++ ) {

    if ( $i % 10 == 9 ) {
      $rides [] = [ 'km' => round ( 18 + 6 * mt_rand () / mt_getrandmax (), 1 ), 'euro' => round ( $bell ( 55, 1.5 ), 2 ) ];
      continue;
    }

    $km = round ( min ( 26, max ( 0.5, -2.5 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) * max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) ), 1 );

    $rides [] = [ 'km' => $km, 'euro' => round ( max ( 4, 3.5 + $km * $bell ( 2.4, 0.35 ) ), 2 ) ];

  }

?>
