<?php


  function padAtValueField ( $field, $cor='' ) { 

    if ( ! str_contains( $field, '@') )
      $field .= '@any';

    return padAtValue ( $field, $cor ); 

  }


  function padAtValueTag ( $field, $cor='' ) { 

    return padAtValue ( $field, $cor); 

  }


  function padAtValue ( $field, $cor='' ) {

    if ( str_contains($field, '@*') )
      return padAtValueAny ( $field, $cor);
    
    $field = rtrim ( $field );

    list ( $before, $after ) = padSplit ( '@', $field );
    
    $names = padExplode ( $before, '.' ); 
    $parts = padExplode ( $after,  '.' ); 

    $at = padAt ( $names, $parts, $cor );

    global $debug;
    $debug [] = ['value', $names, $parts, $cor, $at];

    return $at;

  }


  function padAtValueAny ( $field, $cor ) {

    global $pad;

    for ( $i=$pad; $i; $i-- ) {

      $check = str_replace ( '@*', "@$i", $field );

      if ( padAtCheck ( $check, $cor ) ) 
        return padAtValue ( $check, $cor );
    
    }

    return INF;

  }


?>