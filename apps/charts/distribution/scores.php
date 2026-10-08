<?php

  // The exam scores of four classes of 30, from a fixed seed: a strong class, a weak one,
  // one with a wide spread and one split in two - shapes a box plot would draw alike.

  mt_srand ( 23 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $classes = [ '3A' => fn ( $i ) => $bell ( 78, 7 ),
               '3B' => fn ( $i ) => $bell ( 61, 8 ),
               '3C' => fn ( $i ) => $bell ( 68, 15 ),
               '3D' => fn ( $i ) => $i % 2 ? $bell ( 52, 6 ) : $bell ( 84, 6 ) ];

  $scores = [];

  foreach ( $classes as $class => $score )
    for ( $i = 0; $i < 30; $i++ )
      $scores [] = [ 'class' => $class, 'score' => max ( 10, min ( 100, round ( $score ( $i ) ) ) ) ];

?>
