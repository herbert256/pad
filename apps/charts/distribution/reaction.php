<?php

  // Reaction times in milliseconds of 150 trials rested and 150 after a night without
  // sleep, from a fixed seed: the tired ones slower and more spread, with a long tail of
  // lapses - two bell curves of their own, drawn as densities over one axis.

  mt_srand ( 11 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $trials = [];

  for ( $i = 0; $i < 150; $i++ )
    $trials [] = [ 'group' => 'Rested', 'ms' => round ( $bell ( 285, 32 ) ) ];

  for ( $i = 0; $i < 150; $i++ )
    $trials [] = [ 'group' => 'Sleep-deprived', 'ms' => round ( $i % 6 ? $bell ( 340, 45 ) : $bell ( 480, 60 ) ) ];

?>
