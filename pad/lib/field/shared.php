<?php


  function padFieldGetLevel  ( $search ) {

    global $pad, $padName;

    for ( $i=$pad; $i; $i-- )
      if ( $padName[$i] == $search )
        return $i;

    if ( $search == 'PHP' )
      return 0;

    if ( is_numeric($search) and $search < 0 )
      return $pad - abs($search);

    if ( is_numeric($search) ) 
      return $search;

    if (trim($search) == '' )
      return $pad;

    return $GLOBALS ['pad'];

  } 


  function padFieldDoubleCheck ($first, $seperator, $second) {

    if ( $GLOBALS ['padFieldDoubleCheck'] )
      return ;

    $GLOBALS ['padFieldDoubleCheck'] = TRUE;

    $tag = "$first$seperator$second";
    
    if     ( padFieldCheck($tag) ) return padFieldValue($tag);
    elseif ( padArrayCheck($tag) ) return padArrayValue($tag); 

    $GLOBALS ['padFieldDoubleCheck'] = FALSE;

  }

?>