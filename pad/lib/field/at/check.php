<?php


  function padAtCheckField ( $field, $type='var' ) { 

    if ( ! str_contains( $field, '@') )
      $field .= '@any';

    return padAtCheck ( $field, $type ); 

  }
  
  
  function padAtCheckTag ( $field, $type='var' ) { 

    return padAtCheck ( $field, $type ); 

  }
  

  function padAtCheck ( $field, $type='var' ) {

    $field = rtrim ( $field );

    if ( preg_match ( '/\s/', $field  ) ) return FALSE; 
    if ( substr_count($field, '@') <> 1 ) return FALSE;

    list ( $before, $after ) = padSplit ( '@', $field );
    
    if ( ! strlen ( $before ) ) return FALSE;
    if ( ! strlen ( $after  ) ) return FALSE;

    $names = padExplode ( $before, '.' ); 
    $parts = padExplode ( $after,  '.' ); 

    if ( $parts [0] == '*' )
      return padAtCheckAny ( $field, $type, $names, $parts );

    if ( count ( $parts ) > 2                                 ) return FALSE;
    if (                           ! padAtValid ( $parts[0] ) ) return FALSE;
    if ( count ( $parts ) == 2 and ! padAtValid ( $parts[1] ) ) return FALSE;

    foreach ( $names as $part)
      if ( ! padAtCheckName ($part) )
        return FALSE;

    $at = padAt ( $names, $parts, $type );

    if ( $at === INF )
      return FALSE;

    return TRUE;

  }


  function padAtCheckAny ( $field, $type, $names, $parts ) {

    global $pad;

    for ( $i=$pad; $i; $i-- ) {

      $parts [0] = $i;

      $field = implode ( '.', $names ) . '@' . implode ( '.', $parts );

      if ( padAtCheck ( $field, $type ) === INF ) 
        return TRUE;
    
    }

    return FALSE;

  }


  function padAtCheckName ( $part ) {

    if ( ctype_alpha ( $part) ) return TRUE;
    if ( ctype_digit ( $part) ) return TRUE;
    if ( $part == '*')          return TRUE;

    if ( strlen($part) > 1 ) { 
      $check1 = substr ( $part, 0, 1 );
      $check2 = substr ( $part, 1    );
      if ( $check1 == '<' and ctype_digit ( $check2) ) return TRUE;
      if ( $check1 == '>' and ctype_digit ( $check2) ) return TRUE;
    }

    if ( padAtCheckCondition ( $part, '<>' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '<=' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '>=' ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '>'  ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '<'  ) ) return TRUE;
    if ( padAtCheckCondition ( $part, '='  ) ) return TRUE;

    if ( padAtValid ( $part ) ) return TRUE;

    return FALSE;

  }


  function padAtCheckCondition ( $part, $condition ) {

    if ( ! str_contains ( $part, $condition ) ) 
      return TRUE;

    $parts = explode ( $condition, $part );

    if ( count ( $parts ) <> 2      ) return FALSE;
    if ( ! strlen ( $parts [0] )    ) return FALSE;
    if ( ! strlen ( $parts [1] )    ) return FALSE;
    if ( ! padAtValid ( $part [0] ) ) return FALSE;

    return TRUE;

  }


?>