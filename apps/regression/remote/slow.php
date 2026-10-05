<?php

  // A source that takes its time, for the prefetch case: it answers which one it was, and
  // when it started and ended - two of them overlap when they were served together.

  $padContentType = 'application/json';

  $slowStart = microtime ( TRUE );
  usleep ( 200000 );
  $slowEnd   = microtime ( TRUE );

  echo json_encode ( [ [ 'n' => (int) ( $_GET ['n'] ?? 0 ), 'start' => $slowStart, 'end' => $slowEnd ] ] );

?>
