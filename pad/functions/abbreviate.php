<?php

  // Pipe function abbreviate(precision): a large number made short for people - 1500 is
  // '2K', and with abbreviate(1) '1.5K'; K, M, B, T and Q for thousand up to quadrillion,
  // below 1000 the number without a letter. The template side of padNumberAbbreviate
  // (lib/number.php), which reports a negative precision. A value that is not a number is
  // returned as it is, as the bytes pipe returns one.

  if ( ! is_numeric ( $value ) )
    return $value;

  return padNumberAbbreviate ( $value, $parm [0] ?? 0 );

?>
