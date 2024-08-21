<?php


  function padFieldGetLevel  ( $search ) {

    global $pad, $padName, $padTag;

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

    for ( $i=$pad; $i; $i-- )
      if ( $padTag [$i] == $search )
        return $i;

    return $pad;

  } 


  function padFieldFirstParmTag ($flag=0) {

    global $pad, $padType, $padTag;

    $start = ($flag) ? $pad-1 : $pad;

    for ($i=$start; $i; $i--) // ToDo: Array van make en split/after verwerken
      if ( $padTag [$i] <> 'if' and $padTag [$i] <> 'case' and $padType[$i] <> 'tag' )
        return $i;

    return $pad - 1;

  }  


  function padFieldFirstNonTag ($lvl=0) {

    global $pad, $padType;

    $start = $pad-$lvl;

    for ($i=$start; $i; $i--)
      if ( $padType[$i] <> 'tag' )
        return $i;

    return $pad - 1;

  }  

  
?>