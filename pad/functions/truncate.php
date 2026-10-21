<?php

  // Pipe function truncate(length, ellipsis): shortens the value to at most length
  // characters, ellipsis included, ending on a whole word where one ends in the second half
  // of the cut - {$intro | truncate(80)}. The ellipsis defaults to '…'; a value that fits is
  // returned as it is. Counted in characters, as max_len counts, which cuts without either -
  // the characters a reader sees (functions/_lib/grapheme.php).

  $length   = max ( 0, (int) ( $parm [0] ?? 80 ) );
  $ellipsis = (string) ( $parm [1] ?? '…' );
  $value    = (string) $value;

  include_once PAD . 'functions/_lib/grapheme.php';

  if ( padGraphemeLength ( $value ) <= $length )
    return $value;

  $room = $length - padGraphemeLength ( $ellipsis );

  if ( $room <= 0 )
    return padGraphemeCut ( $ellipsis, 0, $length );

  $cut   = padGraphemeCut ( $value, 0, $room + 1 );
  $space = max ( strrpos ( $cut, ' ' ), strrpos ( $cut, "\n" ) );

  $cut = ( $space !== FALSE and padGraphemeLength ( substr ( $cut, 0, $space ) ) >= $room / 2 )
       ? substr ( $cut, 0, $space )
       : padGraphemeCut ( $cut, 0, $room );

  return rtrim ( $cut, " \t\n\r,;:.-" ) . $ellipsis;

?>
