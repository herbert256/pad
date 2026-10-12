<?php

  // Two hundred events - more than 150 - are laid out one under the other, as vertical
  // lays them: across the page the place of each card took time with the cube of their
  // number, 28 seconds for 800, and a feed or a log of a thousand timed a page out.

  $events = [];

  for ( $i = 0; $i < 200; $i++ )
    $events [] = [ 'when' => sprintf ( '2026-10-%02d', $i % 28 + 1 ), 'what' => "Event $i" ];

?>
