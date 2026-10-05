<?php

  // Pipe function truncate(length, ellipsis): shortens the value to at most length
  // characters, ellipsis included, ending on a whole word where one ends in the second half
  // of the cut - {$intro | truncate(80)}. The ellipsis defaults to '…'; a value that fits is
  // returned as it is. Counted in characters, as max_len counts, which cuts without either.

  $length   = max ( 0, (int) ( $parm [0] ?? 80 ) );
  $ellipsis = (string) ( $parm [1] ?? '…' );
  $value    = (string) $value;

  if ( mb_strlen ( $value, 'UTF-8' ) <= $length )
    return $value;

  $room = $length - mb_strlen ( $ellipsis, 'UTF-8' );

  if ( $room <= 0 )
    return mb_substr ( $ellipsis, 0, $length, 'UTF-8' );

  $cut   = mb_substr ( $value, 0, $room + 1, 'UTF-8' );
  $space = max ( mb_strrpos ( $cut, ' ', 0, 'UTF-8' ), mb_strrpos ( $cut, "\n", 0, 'UTF-8' ) );

  $cut = ( $space !== FALSE and $space >= $room / 2 )
       ? mb_substr ( $cut, 0, $space, 'UTF-8' )
       : mb_substr ( $cut, 0, $room, 'UTF-8' );

  return rtrim ( $cut, " \t\n\r,;:.-" ) . $ellipsis;

?>
