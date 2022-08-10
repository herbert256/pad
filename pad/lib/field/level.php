<?php


  function pField_level ( $field, $type ) {

    global $p, $pCurrent, $pPrmsTag;

    if ( is_numeric($field) ) 
      return pField_level_nr ($field);

    for ( $i=$p; $i; $i-- )
      if ( isset ( $pCurrent [$i] [$field] ) ) {
        $work = $pCurrent [$i] [$field];
        if     (   is_array ( $work ) and ( $type == 3 or $type == 4 ) ) return $work;
        elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 ) ) return $work;
      }

    for ( $i=$p; $i; $i-- ) {

      if ( $i == 1 )
        $work = pField_search ( $GLOBALS, $field, $type );
      else
        $work = pField_search ( $pCurrent [$i], $field, $type );

      if ( $work !== INF )
        return $work;

    }

    for ( $i=$p; $i; $i-- )
      if ( isset ( $pPrmsTag [$i] [$field] ) )
        return $pPrmsTag [$i] [$field];

    for ( $i=$p; $i; $i-- )
      if ( $pName[$i] ) {
        $work = pField_tag ( $pName[$i] . '#' . $field );
        if ( $work !== INF )
          return $work;
      }

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