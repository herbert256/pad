<?php

  function padFieldLevel ( $field, $type ) {

    global $pad, $padCurrent, $padPrm, $padName;

    if ( is_numeric($field) )
      if ( isset ( $padPrm [$pad] [$field] ) )
        return $padPrm [$pad] [$filed]; 
      else
        return INF;

    for ( $i=$pad; $i; $i-- )
      if ( array_key_exists ( $field, $padCurrent [$i] ) ) {
        $work = $padCurrent [$i] [$field];
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

    if ( array_key_exists ( $field, $GLOBALS ) ) {
      $work = $GLOBALS [$field];
      if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
      elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
    }

    return INF;
    
  }


?>