<?php

  if ( is_array($value) ) {
    foreach ( $value as $pad_k3)
      if ( ! in_array($pad_k3, $parm) )
        return FALSE;
    return TRUE;
  }

  return ( in_array($value, $parm) ) ? '1' : '';

?>