<?php

  // Every view uses the engine's shared padDateParse/padNow timezone-aware date handling.
  include_once PAD . 'subs/times/_lib/countdown.php';
  include_once PAD . 'subs/times/_lib/timeago.php';
  include_once PAD . 'subs/times/_lib/time.php';

  if ( ! padKind ( 'times', $padTimeKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no time display named '" . padMakeSafe ( $padTimeKind, 20 ) . "'" );
    return FALSE;
  }
  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'time', $padTimeKind );

  return include PAD . "subs/times/types/$padTimeKind.php";

?>
