<?php

  // Loop build for nand: the bitwise NAND of the loop value and the parameter,
  // ~($pqLoop & $pqParm) - and/loop.php does the AND and this complements its result.

  // No term from and/loop.php - a value that is no whole number - is none here either: ~ of
  // the FALSE ended the request.

  $pqBitwise = include PT . 'and/loop.php';

  return ( $pqBitwise === FALSE ) ? FALSE : ~ $pqBitwise;

?>
