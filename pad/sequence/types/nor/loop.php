<?php

  // Loop build for nor: the bitwise NOR of the loop value and the parameter,
  // ~($pqLoop | $pqParm) - or/loop.php does the OR and this complements its result.

  // No term from or/loop.php - a value that is no whole number - is none here either: ~ of
  // the FALSE ended the request.

  $pqBitwise = include PT . 'or/loop.php';

  return ( $pqBitwise === FALSE ) ? FALSE : ~ $pqBitwise;

?>
