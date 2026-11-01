<?php

  // With the strict check off a text a format cannot read - a key without its value, an
  // event without its end - is no rows and no error, as an unknown data type falls back
  // there; a row a writer has no place for is left out without a word.

  $padCheckSyntax = FALSE;

  $lenientIcs = padFormatWrite ( 'ics', [ [ 'summary' => 'no date' ], [ 'summary' => 'dated', 'start' => '2026-11-01' ] ] );
  $lenientIcs = substr_count ( $lenientIcs, 'BEGIN:VEVENT' ) . ' event';

?>
