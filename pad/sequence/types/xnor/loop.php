<?php

  // Loop build for xnor: the bitwise XNOR of the loop value and the parameter,
  // ~($pqLoop ^ $pqParm) - xor/loop.php does the XOR and this complements its result.

  // No term from xor/loop.php - a value that is no whole number - is none here either: ~ of
  // the FALSE ended the request.

  $pqBitwise = include PT . 'xor/loop.php';

  return ( $pqBitwise === FALSE ) ? FALSE : ~ $pqBitwise;

?>
