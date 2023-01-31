<?php


  function padFieldGetLevel  ( $search ) {

    global $pad, $padName;

    if (trim($search) == '' )
      return $pad;

    for ( $i=$pad; $i; $i-- )
      if ( $padName[$i] == $search )
        return $i;

    if ( $search == 'PHP' )
      return 0;

    if ( is_numeric($search) and $search < 0 )
      return $pad - abs($search);

    if ( is_numeric($search) ) 
      return $search;

    return $GLOBALS ['pad'];

  } 


?>