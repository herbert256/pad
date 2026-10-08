<?php

  // The average temperature of every month in nine cities, in °C: the north and the south
  // of Europe, a continental winter, the tropics that hardly change, and two cities south
  // of the equator whose summer falls in January.

  $normals = [
    'Oslo'      => [ -4, -4,  0,  5, 11, 15, 17, 16, 11,  6,  1, -3 ],
    'Amsterdam' => [  3,  4,  6,  9, 13, 16, 18, 18, 15, 11,  7,  4 ],
    'Madrid'    => [  6,  8, 11, 13, 17, 23, 26, 25, 21, 15, 10,  7 ],
    'Moscow'    => [ -7, -6, -1,  6, 13, 17, 19, 17, 11,  5, -1, -5 ],
    'Cairo'     => [ 14, 15, 18, 22, 25, 27, 28, 28, 26, 24, 19, 15 ],
    'Singapore' => [ 27, 27, 28, 28, 28, 28, 28, 28, 28, 28, 27, 27 ],
    'Tokyo'     => [  5,  6,  9, 14, 19, 22, 26, 27, 24, 18, 13,  8 ],
    'Sydney'    => [ 23, 23, 22, 19, 16, 14, 13, 14, 16, 18, 20, 22 ],
    'Cape Town' => [ 22, 22, 21, 18, 16, 14, 13, 14, 15, 17, 19, 21 ] ];

  $months  = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
  $climate = [];

  foreach ( $normals as $city => $temperatures )
    foreach ( $temperatures as $m => $temperature )
      $climate [] = [ 'city' => $city, 'month' => $months [$m], 'temperature' => $temperature ];

?>
