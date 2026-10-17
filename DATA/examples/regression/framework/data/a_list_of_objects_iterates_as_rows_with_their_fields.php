<?php

  $decoded = json_decode ( '[{"a":1,"b":"x"},{"a":2,"b":"y"}]' );
  $people  = [ (object) [ 'name' => 'P1', 'age' => 30 ], (object) [ 'name' => 'P2', 'age' => 40 ] ];
  $nested  = json_decode ( '[{"addr":{"city":"Paris"},"tags":[{"t":1},{"t":2}]}]' );
  $dates   = [ new DateTimeImmutable ( '2026-01-02 03:04:05', new DateTimeZone ( 'UTC' ) ) ];

?>
