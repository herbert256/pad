<?php


  function padFieldGetLevel  ( $search ) {

    global $pad, $padName;

    if ( trim($search) == '' )
      return $pad;

    for ( $i=$pad; $i; $i-- )
      if ( $padName [$i] == $search )
        return $i;

    if ( $search == 'PHP' )
      return 0;

    if ( is_numeric($search) and $search < 0 )
      return $pad + $search;

    if ( is_numeric($search) ) 
      return $search;

    return $pad;

  } 


  function padFieldFirstNonTag  ($flag=0) {

    global $pad, $padType;

    $start = ($flag) ? $pad-1 : $pad;

    for ($i=$start; $i; $i--)
      if ( $padType[$i] and $padType[$i] <> 'tag' )
        return $i;

    return $pad - 1;

  }  

  
?>