<?php


  function padFieldLevel ( $field, $type ) {

    global $pad, $padCurrent, $padPrm, $padName;

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

    global $pad, $padPrm;
    
    if ( isset ( $padPrm [$pad] [$nr] ) )
      return $padPrm [$pad] [$nr]; 
    else
      return INF;

  }


?>