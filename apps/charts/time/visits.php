<?php

  // Eight weeks of visits to an office tool: busy working days, Monday the busiest, a
  // quiet weekend - and a site that slowly grows, with a holiday week in the middle.

  $visits = [];
  $days   = [ 'Mon' => 1.15, 'Tue' => 1.05, 'Wed' => 1.0, 'Thu' => 0.98, 'Fri' => 0.8, 'Sat' => 0.3, 'Sun' => 0.25 ];

  for ( $week = 1; $week <= 8; $week++ )
    foreach ( $days as $day => $busy ) {

      $count = 900 * ( 1 + 0.04 * $week ) * $busy * ( $week == 5 ? 0.45 : 1 );

      $visits [] = [ 'week' => "W$week", 'day' => $day, 'visits' => (int) round ( $count ) ];

    }

?>
