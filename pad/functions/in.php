<?php

  if ( is_array($value) ) {
    foreach ( $value as $pK3)
      if ( ! in_array($pK3, $parm) )
        return FALSE;
    return TRUE;
  }

  return ( in_array($value, $parm) ) ? '1' : '';

?>