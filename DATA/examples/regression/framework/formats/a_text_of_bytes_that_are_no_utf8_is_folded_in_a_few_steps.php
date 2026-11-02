<?php

  // A latin1 or cp1252 row - a rule of em dashes, 0x97 - holds bytes that are no UTF-8: the
  // calendar and the card fold its line in a few steps all the same, each such byte a ? as
  // the XML of a feed has it, where the fold cut nothing off a run of 74 of them and grew the
  // text until the memory ran out. The limit makes a return of that loop fail in a moment.

  ini_set ( 'memory_limit', '256M' );

  $foldRule = 'Agenda ' . str_repeat ( "\x97", 80 ) . ' end';

  $foldIcs = padFormatWrite ( 'ics', [ [ 'summary' => 'Meeting', 'start' => '2026-11-01', 'description' => $foldRule ] ] );
  $foldVcf = padFormatWrite ( 'vcf', [ [ 'name' => str_repeat ( "\xBF", 80 ), 'uid' => 'u1' ] ] );

  $foldShow = fn ( $text ) => implode ( '~', array_filter ( explode ( "\r\n", $text ), fn ( $line ) => preg_match ( '/^(DESCRIPTION|FN|\s)/', $line ) ) );

  $foldIcsShow = $foldShow ( $foldIcs );
  $foldVcfShow = $foldShow ( $foldVcf );
  $foldLong    = max ( array_map ( 'strlen', explode ( "\r\n", $foldIcs . $foldVcf ) ) );

?>
