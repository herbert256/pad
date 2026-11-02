<?php

  // Events at moments counted from now - from half a minute ahead, so the words come out
  // round - and the page always has some to come.

  $events = [];
  $from   = padNow ()->modify ( '+30 seconds' );

  foreach ( [ 'Jazz in the park'   => '-2 days',
              'Pottery workshop'   => '+3 hours 20 minutes',
              'City half marathon' => '+4 days 6 hours',
              'Film night'         => '+26 days 2 hours',
              'New year concert'   => '+84 days 3 hours' ] as $name => $when )
    $events [] = [ 'name' => $name, 'starts' => $from->modify ( $when )->format ( 'Y-m-d H:i:s' ) ];

?>
