<?php

  // A quoted value of the front matter as a PHP without the yaml extension reads it, of any
  // length: past some nine thousand characters the quoted patterns ran out of PCRE's JIT
  // stack, the value kept its quotes and was cut at its first " #".

  $double = str_repeat ( 'word # ', 3000 );
  $single = str_repeat ( "it's ", 3000 );
  $lines  = padFrontMatterLines ( "summary: \"$double\"\nnote: '" . str_replace ( "'", "''", $single ) . "'" );

  $summary = ( $lines ['summary'] ?? '' ) === $double ? 'whole' : 'cut';
  $note    = ( $lines ['note']    ?? '' ) === $single ? 'whole' : 'cut';

?>
