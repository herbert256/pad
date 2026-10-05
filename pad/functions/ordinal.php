<?php

  // Pipe function ordinal: a number as a place in English - 1st, 2nd, 3rd, 4th, 11th, 21st,
  // 112th - for a ranking or a date's day. The template side of padNumberOrdinal
  // (lib/number.php), which reports a number with a fraction. A value that is not a number is
  // returned as it is.

  if ( ! is_numeric ( $value ) )
    return $value;

  return padNumberOrdinal ( $value );

?>
