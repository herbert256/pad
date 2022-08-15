<?php


  function pFieldGetLevel  ( $search ) {

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


  function pFieldDoubleCheck ($first, $seperator, $second) {

    if ( $GLOBALS ['padField_double_check'] )
      return ;

    $GLOBALS ['padField_double_check'] = TRUE;

    $tag = "$first$seperator$second";
    
    if     ( pField_check($tag) ) return pField_value($tag);
    elseif ( pArray_check($tag) ) return pArray_value($tag); 

    $GLOBALS ['padField_double_check'] = FALSE;

  }

?>