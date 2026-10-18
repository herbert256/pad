<?php

  // Pipe function contains(needle): true when the needle occurs anywhere in the value.
  // Case-sensitive, and it hands back real booleans rather than the '1'/'' that in and
  // like return.
  //
  // A list contains an item when it holds one equal to the needle, as in tests it: array:rows
  // contains 2. Handed to strpos, the list ended the request on a string wanted.

  if ( is_array ( $value ) )
    return in_array ( $parm [0], $value );

  if ( strpos($value, $parm[0]) !== FALSE )
    return TRUE;
  else
    return FALSE;

?>
