<?php

 function padAtCheck ( $field ) {

    if ( str_ends_with ( $field, '@' ) )
      $field .= '*';
    
    if ( str_contains($field, '@*') )
      return padAtCheck ( str_replace ( '@*', "@1", $field ) );

    $field = rtrim ( $field );

    if ( preg_match ( '/\s/', $field  ) ) return FALSE; 
    if ( substr_count($field, '@') <> 1 ) return FALSE;

    list ( $before, $after ) = padSplit ( '@', $field );
    
    if ( ! strlen ( $before )            ) return FALSE;
    if ( ! strlen ( $after  )            ) return FALSE;
    if ( str_starts_with ( $before, '.') ) return FALSE;
    if ( str_starts_with ( $after,  '.') ) return FALSE;
    if ( str_ends_with   ( $before, '.') ) return FALSE;
    if ( str_ends_with   ( $after,  '.') ) return FALSE;
  
    $names = padExplode ( $before, '.' ); 
    $parts = padExplode ( $after,  '.' ); 

    foreach ( $parts as $part)
      if ( ! padAtCheckPart ($part) )
        return FALSE;

    foreach ( $names as $part)
      if ( ! padAtCheckNamePart ($part) )
        return FALSE;

    return TRUE;

  }


  function padAtCheckPart ( $part ) {

    if ( is_numeric  ( $part ) ) return TRUE;
    if ( ctype_alpha ( $part ) ) return TRUE;
    if ( ctype_digit ( $part ) ) return TRUE;
    if ( padAtValid  ( $part ) ) return TRUE;

    return FALSE;

  }


  function padAtCheckNamePart ( $part ) {

    if ( ctype_alpha ( $part) ) return TRUE;
    if ( ctype_digit ( $part) ) return TRUE;
    if ( $part == '*')          return TRUE;
    if ( $part == '<')          return TRUE;
    if ( $part == '>')          return TRUE;

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