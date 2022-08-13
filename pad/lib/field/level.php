<?php


  function pField_level ( $field, $type ) {

    global $p, $pCurrent, $pPrmsTag, $pName;

    if ( is_numeric($field) ) 
      return pField_level_nr ($field);

    for ( $i=$p; $i; $i-- )
      if ( array_key_exists ( $field, $pCurrent [$i] ) ) {
        $work = $pCurrent [$i] [$field];
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


  function pField_level_nr ($nr) {

    global $p, $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$p] [$nr-1] ) )
      return $pPrmsVal[$p] [$nr-1]; 
    else
      return INF;

  }


?>