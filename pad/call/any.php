<?php

  // Calls $padCall and returns whatever the file returned, with any non-blank echoed output
  // appended. The one wrapper that does not force the result to a string, so it is used
  // where a PHP file may hand back a data array - callbacks, react providers, {local:...}
  // files and app functions.

  include PAD . 'call/_call.php';

  // A file with no return statement hands back PHP's bare 1, which is not a result to put
  // in front of what it echoed - nor is a return TRUE, which only says the call went well:
  // appended to, it became the text 1 and the answer '1ABC'. A data array has nothing to
  // append to; and an echoed 0 is output like any other.

  if ( trim ( $padCallOB ) !== '' )
    if ( $padCallPHP === 1 or $padCallPHP === TRUE )
      $padCallPHP = $padCallOB;
    elseif ( ! is_array ( $padCallPHP ) and ! is_object ( $padCallPHP ) )
      $padCallPHP .= $padCallOB;

  return $padCallPHP;

?>