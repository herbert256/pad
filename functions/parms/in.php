<?php

  if ( is_array($value) ) {
    foreach ( $value as $padK3)
      if ( ! in_array($padK3, $parm) )
        return FALSE;
    return TRUE;
  }

  return ( in_array($value, $parm) ) ? '1' : '';

?>