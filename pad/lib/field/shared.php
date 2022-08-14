<?php


  function pFieldGetLevel  ( $search ) {

    global $p, $pName;

    for ( $i=$p; $i; $i-- )
      if ( $pName[$i] == $search )
        return $i;

    if ( $search == 'PHP' )
      return 0;

    if ( is_numeric($search) and $search < 0 )
      return $p - abs($search);

    if ( is_numeric($search) ) 
      return $search;

    if (trim($search) == '' )
      return $p;

    return $GLOBALS ['p'];

  } 


  function pFieldDoubleCheck ($first, $seperator, $second) {

    if ( $GLOBALS ['pField_double_check'] )
      return ;

    $GLOBALS ['pField_double_check'] = TRUE;

    $tag = "$first$seperator$second";
    
    if     ( pField_check($tag) ) return pField_value($tag);
    elseif ( pArray_check($tag) ) return pArray_value($tag); 

    $GLOBALS ['pField_double_check'] = FALSE;

  }

?>