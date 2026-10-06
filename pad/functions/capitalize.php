<?php

  // Pipe function capitalize: upper-cases the first letter of every word, leaving the rest
  // of each word alone; ucwords is the same function under its PHP name. In UTF-8, so éric
  // becomes Éric - PHP's ucwords knows ASCII only. Words end at whitespace, as ucwords has
  // them.
  //
  // A value that is not valid UTF-8 has its broken bytes made a ? first, as upper and lower
  // have them: the u pattern answered NULL for it, and one broken byte blanked the value.

  $padCapText = (string) $value;

  if ( ! mb_check_encoding ( $padCapText, 'UTF-8' ) )
    $padCapText = mb_convert_encoding ( $padCapText, 'UTF-8', 'UTF-8' );

  return preg_replace_callback ( '/(^|\s)(\X)/u', fn ( $m ) => $m [1] . mb_strtoupper ( $m [2], 'UTF-8' ), $padCapText );

?>
