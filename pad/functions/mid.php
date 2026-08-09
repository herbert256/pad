<?php

  // Pipe function mid(start, length): substring with a 1-based start position, so mid(7, 5)
  // on 'Hello World' yields 'World'. A start below 1 is read as 1 - the documented base -
  // where it used to slide into substr's negative offsets and answer from the far end. A
  // negative or zero length names no characters and answers the empty string; no length at
  // all means the rest of the value.

  $padMidStart = max ( (int) $parm [0], 1 );
  $padMidLen   = $parm [1] ?? NULL;

  if ( $padMidLen !== NULL and (int) $padMidLen < 1 )
    return '';

  return substr ( $value, $padMidStart - 1, $padMidLen === NULL ? NULL : (int) $padMidLen );

?>