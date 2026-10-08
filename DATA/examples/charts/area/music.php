<?php

  // Two years of listening, in hours per month: pop steady with a summer swell, rock
  // fading, hip-hop rising, electronic peaking each summer, jazz and classical in winter.

  $listening = [];
  $months    = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];

  for ( $m = 0; $m < 24; $m++ ) {

    $summer = cos ( ( $m % 12 - 6.5 ) / 12 * 2 * M_PI );
    $winter = - $summer;

    $listening [] = [
      'month'      => $months [$m % 12] . ' ' . ( 2025 + intdiv ( $m, 12 ) ),
      'pop'        => (int) round ( 42 + 10 * $summer + 4 * sin ( $m / 2 ) ),
      'rock'       => (int) round ( 38 - $m * 1.1 + 5 * sin ( $m / 3 + 1 ) ),
      'hiphop'     => (int) round ( 8 + $m * 1.6 + 3 * sin ( $m / 2.5 ) ),
      'electronic' => (int) round ( 12 + 16 * max ( 0, $summer ) ** 2 ),
      'jazz'       => (int) round ( 9 + 7 * max ( 0, $winter ) ),
      'classical'  => (int) round ( 6 + 9 * max ( 0, $winter ) ** 3 + ( $m % 12 == 11 ? 8 : 0 ) ) ];

  }

?>
