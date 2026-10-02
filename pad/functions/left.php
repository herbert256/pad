<?php

  // Pipe function left(n): the first n characters of the value, or the whole value when it
  // is shorter. A count of zero or less names no characters and answers the empty string -
  // a negative count used to fall through to substr, which read it as trim-from-the-end.
  // Characters, not bytes: a cut through a UTF-8 letter left a broken byte that the default
  // sanitize step then turned into an empty value.

  $padLeft = (int) $parm [0];

  if ( $padLeft < 1 )
    return '';

  return mb_substr ( $value, 0, $padLeft, 'UTF-8' );

?>