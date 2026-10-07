<?php

  // Visits per weekday and hour: a busy lunch, a busier evening, a quiet weekend morning,
  // and a week that gets busier towards Thursday and quieter on Friday night.

  $traffic = [];
  $week    = [ 'Mon' => 0.85, 'Tue' => 0.95, 'Wed' => 1.0, 'Thu' => 1.1, 'Fri' => 0.9, 'Sat' => 1.2, 'Sun' => 1.3 ];

  foreach ( $week as $day => $busy )
    foreach ( [ 8, 10, 12, 14, 16, 18, 20, 22 ] as $hour ) {

      $visits = 40 + 60 * exp ( - ( ( $hour - 12.5 ) ** 2 ) / 3 )
                   + 90 * exp ( - ( ( $hour - 20 )   ** 2 ) / 4 );

      $visits = $visits * $busy - ( $busy > 1.15 and $hour < 12 ? 35 : 0 );

      $traffic [] = [ 'day' => $day, 'hour' => "{$hour}h", 'visits' => (int) round ( $visits ) ];

    }

?>
