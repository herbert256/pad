<?php

  // Calls a plain PHP function - the handler behind the php: prefix.
  //
  // With no arguments written in the template the piped-in $value becomes the single argument,
  // so {echo $s | php:strtoupper} works; otherwise $parm is passed through as the argument
  // list and $value is ignored unless the template placed it there with @.

  if ( ! count ($parm) and $value )
    $parm [0] = $value;

  return call_user_func_array ($name, $parm);

?>