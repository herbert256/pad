<?php

  // Calls $padCall and returns whatever the file returned, with any non-blank echoed output
  // appended. The one wrapper that does not force the result to a string, so it is used
  // where a PHP file may hand back a data array - callbacks, react providers, {local:...}
  // files and app functions.

  include PAD . 'call/_call.php';

  if ( trim ( $padCallOB ) )
    $padCallPHP .= $padCallOB;

  return $padCallPHP;

?>