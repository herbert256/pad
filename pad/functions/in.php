<?php

  if ( is_array($value) ) {
    foreach ( $value as $padK3)
      if ( ! in_array($padK3, $padarm) )
        return FALSE;
    return TRUE;
  }

  return ( in_array($value, $padarm) ) ? '1' : '';

?>