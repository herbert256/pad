<?php

  // Pipe function in(a, b, ...): membership test returning '1' or ''. A single array
  // argument is unwrapped and used as the list. When the piped value is itself an array the
  // test becomes subset containment - true only if every element of it is in the list.

  if ( count ( $parm ) == 1 and is_array ( $parm [0] ) )
    $parm = $parm [0];

  if ( is_array ($value) ) {
    foreach ( $value as $padK3)
      if ( ! in_array($padK3, $parm) )
        return FALSE;
    return TRUE;
  }

  return ( in_array($value, $parm) ) ? '1' : '';

?>