<?php

  // A source that takes its time: it answers when it started and when it ended, so the
  // prefetch case can tell whether two of them were on the wire together.

  $padContentType = 'application/json';

  $slowStart = microtime ( TRUE );
  usleep ( 300000 );
  $slowEnd   = microtime ( TRUE );

  echo json_encode ( [ [ 'n' => (int) ( $_GET ['n'] ?? 0 ), 'start' => $slowStart, 'end' => $slowEnd ] ] );

?>
