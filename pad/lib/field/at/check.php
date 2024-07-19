<?php


  function padAtCheckField ( $field, $cor='' ) { 

    if ( ! str_contains( $field, '@') )
      $field .= '@any';

    return padAtCheck ( $field, $cor ); 

  }
  
  
  function padAtCheckTag ( $field, $cor='' ) { 

    return padAtCheck ( $field, $cor ); 

  }
  

  function padAtCheck ( $field, $cor='' ) {

    if ( str_contains($field, '@*') )
      return padAtCheckAny ( $field, $cor);

    $field = rtrim ( $field );

    if ( preg_match ( '/\s/', $field  ) ) return FALSE; 
    if ( substr_count($field, '@') <> 1 ) return FALSE;

    list ( $before, $after ) = padSplit ( '@', $field );
    
    if ( ! strlen ( $before ) ) return FALSE;
    if ( ! strlen ( $after  ) ) return FALSE;

    $names = padExplode ( $before, '.' ); 
    $parts = padExplode ( $after,  '.' ); 

    foreach ( $parts as $part)
      if ( ! padAtCheckPart ($part) )
        return FALSE;

    foreach ( $names as $part)
      if ( ! padAtCheckName ($part) )
        return FALSE;

    $at = padAt ( $names, $parts, $cor );

    global $debug;
    $debug [] = ['check', $names, $parts, $cor, $at];
  
    if ( $at === INF )
      return FALSE;

    return TRUE;

  }


  function padAtCheckAny ( $field, $cor  ) {

    global $pad;

    for ( $i=$pad; $i; $i-- ) {

      $check = str_replace ( '@*', "@$i", $field );

      global $debug2;
      $debug2 [] = ['check-any', $check];

      if ( padAtCheck ( $check, $cor ) ) 
        return TRUE;
    
    }

    return FALSE;

  }


  function padAtCheckPart ( $part ) {

    if ( is_numeric  ( $part) ) return TRUE;
    if ( ctype_alpha ( $part) ) return TRUE;
    if ( ctype_digit ( $part) ) return TRUE;
    if ( $part == '*')          return TRUE;
    if ( padAtValid ( $part ) ) return TRUE;

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