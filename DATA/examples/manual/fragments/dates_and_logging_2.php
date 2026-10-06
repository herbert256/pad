<?php

  padNowFreeze ( '2026-10-05 14:30:00' );

  $comments = [
    [ 'author' => 'Ann',  'posted' => '2026-10-05 14:29:55' ],
    [ 'author' => 'Bob',  'posted' => '2026-10-05 14:12:00' ],
    [ 'author' => 'Cleo', 'posted' => '2026-10-04 09:00:00' ],
    [ 'author' => 'Dirk', 'posted' => '2026-09-20 18:00:00' ],
    [ 'author' => 'Eve',  'posted' => '2025-06-01' ]
  ];

  $deadline = padAgo ( '2026-10-08 12:00' );

?>
