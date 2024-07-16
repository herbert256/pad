<?php


  function padAtValueField ( $field ) { 

    if ( ! str_contains( $field, '@') )
      $field .= '@any';

    return padAtValue ( $field, 'var' ); 

  }


  function padAtValueTag ( $field ) { 

    return padAtValue ( $field, 'tag' ); 

  }


  function padAtValue ( $field, $type='var' ) {

    $field = rtrim ( $field );

    list ( $before, $after ) = padSplit ( '@', $field );
    
    $names = padExplode ( $before, '.' ); 
    $parts = padExplode ( $after,  '.' ); 

    if ( $parts [0] == '*' )
      return padAtValueAny ( $field, $type, $names, $parts );

    return padAt ( $names, $parts, $type );

  }


  function padAtValueAny ( $field, $type, $names, $parts ) {

    global $pad;

    for ( $i=$pad; $i; $i-- ) {

      $parts [0] = $i;

      $field = implode ( '.', $names ) . '@' . implode ( '.', $parts );

      if ( padAtCheck ( $field, $type ) !== INF ) {
        return padAt ( $names, $parts, $type );
      }
    
    }

    return INF;

  }


?>