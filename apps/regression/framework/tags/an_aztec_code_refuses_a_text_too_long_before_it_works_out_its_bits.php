<?php

  // An aztec code of a text no symbol can hold is refused before its bits are worked out - the
  // walk that finds the fewest bits keeps some 3 MB of memory for every 1000 characters, so
  // 60,000 characters took 170 MB before the answer was that they do not fit, and a text of
  // a request put in {aztec} could end the request at a stock memory limit. The largest
  // symbol holds 1664 codewords of 12 bits less the error correction; at the best rate of 2.5
  // bits a character that is at most 7569 characters (ecc=5), 6134 at the default 23%. The
  // longest texts that still fit are unchanged: 7564 characters of '. ' at ecc=5, 3832 digits.

  include_once PAD . 'subs/codes/_lib/aztec.php';

  $long = substr ( str_repeat ( 'Hello, World! 123 ', 4000 ), 0, 60000 );

  memory_reset_peak_usage ();

  $before  = memory_get_usage ();
  $refused = padAztecMatrix ( $long ) === NULL ? 'refused' : 'drawn';
  $memory  = memory_get_peak_usage () - $before < 1000000 ? 'under 1 MB' : 'more than 1 MB';

  $fits = [];

  foreach ( [ [ str_repeat ( '. ', 3782 ), 5 ], [ str_repeat ( '. ', 3783 ), 5 ], [ str_repeat ( '7', 3832 ), 23 ], [ str_repeat ( '7', 3833 ), 23 ] ] as list ( $text, $ecc ) ) {
    $matrix = padAztecMatrix ( $text, $ecc );
    $fits [] = [ 'fit' => strlen ( $text ) . " at $ecc%: " . ( $matrix ? count ( $matrix ) . ' modules' : 'refused' ) ];
  }

?>
