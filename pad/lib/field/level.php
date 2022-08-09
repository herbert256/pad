<?php


  function pField_level ( $field, $type ) {

    global $p, $pCurrent;

    if ( is_numeric($field) ) 
      return pField_nr ($field);

    for ( $i=$p; $i; $i-- ) {

      if ( $i == 1 )
        $work = pField_search ( $GLOBALS, $field, $type );
      else
        $work = pField_search ( $pCurrent[$i], $field, $type );

      if ( $work !== INF )
        return $work;

    }

    return INF;
    
  }


  function pField_nr ($nr) {

    global $p, $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$p-1] [$nr-1] ) )
      return $pPrmsVal[$p-1] [$nr-1]; 
    else
      return INF;

  }


?>