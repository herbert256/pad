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


    for ( $i=$p; $i; $i-- )
      if ( isset ( $pPrmsTag [$i] [$field] ) )
        return $pPrmsTag [$i] [$field];

    return INF;
    
  }


  function pField_level_nr ($nr) {

    global $p, $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$p-1] [$nr-1] ) )
      return $pPrmsVal[$p-1] [$nr-1]; 
    else
      return INF;

  }


?>