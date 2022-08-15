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

    return p();

  } 


  function padFieldDoubleCheck ($first, $seperator, $second) {

    if ( $GLOBALS ['padField_double_check'] )
      return ;

    $GLOBALS ['padField_double_check'] = TRUE;

    $tag = "$first$seperator$second";
    
    if     ( padFieldCheck($tag) ) return padFieldValue($tag);
    elseif ( padArrayCheck($tag) ) return padArrayValue($tag); 

    $GLOBALS ['padField_double_check'] = FALSE;

  }

?>