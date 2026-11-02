<?php

  // The highest temperature of every day of a year, in °C, made from a fixed seed: a mild
  // sea climate - around 6 in January, around 23 in July - each day the month's mean and a
  // bell of its own, wider in spring and summer, when a warm spell and a cool one take turns.

  mt_srand ( 5 );

  $bell = fn ( $mean, $sd ) => $mean + $sd * sqrt ( -2 * log ( max ( 1e-9, mt_rand () / mt_getrandmax () ) ) ) * cos ( 2 * M_PI * mt_rand () / mt_getrandmax () );

  $months = [ 'January' => [ 31, 6, 3 ],    'February' => [ 28, 7, 3.5 ], 'March'    => [ 31, 10, 3.5 ],
              'April'   => [ 30, 14, 4 ],   'May'      => [ 31, 18, 4.5 ], 'June'     => [ 30, 21, 4.5 ],
              'July'    => [ 31, 23, 4.5 ], 'August'   => [ 31, 23, 4 ],   'September' => [ 30, 19, 3.5 ],
              'October' => [ 31, 15, 3 ],   'November' => [ 30, 10, 3 ],   'December' => [ 31, 7, 3 ] ];

  $days = [];

  foreach ( $months as $month => list ( $count, $mean, $sd ) )
    for ( $day = 1; $day <= $count; $day++ )
      $days [] = [ 'month' => $month, 'celsius' => round ( $bell ( $mean, $sd ), 1 ) ];

?>
