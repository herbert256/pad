<?php

  // Pipe function contains(needle): true when the needle occurs anywhere in the value.
  // Case-sensitive, and it hands back real booleans rather than the '1'/'' that in and
  // like return.

  if ( strpos($value, $parm[0]) !== FALSE )
    return TRUE;
  else
    return FALSE;

?>