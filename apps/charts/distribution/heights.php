<?php

  // The heights of 400 adults in centimetres: two bell curves of men and women together,
  // from a fixed seed - so a distribution with two humps rather than one.

  mt_srand ( 7 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $people = [];

  for ( $i = 0; $i < 400; $i++ )
    $people [] = [ 'height' => round ( $i % 2 ? $bell ( 167, 6.5 ) : $bell ( 181, 7 ), 1 ) ];

?>
