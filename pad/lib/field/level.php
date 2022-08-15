<?php


  function padFieldLevel ( $field, $type ) {

    global $pad, $padCurrent, $padPrmsTag, $padName;

    if ( is_numeric($field) ) 
      return padFieldLevelNr ($field);

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


  function padFieldLevelNr ($nr) {

    global $pad, $padPrmsVal;
    
    if ( isset ( $padPrmsVal[$pad] [$nr-1] ) )
      return $padPrmsVal[$pad] [$nr-1]; 
    else
      return INF;

  }


?>