<?php

 /**
  * Validates the syntax of an @ field reference.
  *
  * Checks that the field contains exactly one @ symbol, doesn't contain
  * whitespace, and has valid characters in both the name and level parts.
  *
  * @param string $field The @ field reference to validate.
  *
  * @return bool TRUE if the field syntax is valid, FALSE otherwise.
  */
 function padAtCheck ( $field ) {

    if ( str_ends_with ( $field, '@' ) )
      $field .= '*';
    
    if ( str_contains($field, '@*') )
      return padAtCheck ( str_replace ( '@*', "@1", $field ) );

    $field = rtrim ( $field );

    if ( preg_match ( '/\s/', $field  ) ) return FALSE; 
    if ( substr_count($field, '@') <> 1 ) return FALSE;

    padSplit ( '@', $field, $before, $after );
    
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


  /**
   * Validates a single part of the level specifier in an @ reference.
   *
   * Accepts numeric values, alphabetic strings, digit strings, or
   * parts that pass the padAtValid() check.
   *
   * @param string $part The level specifier part to validate.
   *
   * @return bool TRUE if the part is valid, FALSE otherwise.
   */
  function padAtCheckPart ( $part ) {

    if ( is_numeric  ( $part ) ) return TRUE;
    if ( ctype_alpha ( $part ) ) return TRUE;
    if ( ctype_digit ( $part ) ) return TRUE;
    if ( padAtValid  ( $part ) ) return TRUE;

    return FALSE;

  }


  /**
   * Validates a single part of the name segment in an @ reference.
   *
   * Accepts alphabetic strings, digit strings, wildcards (*), directional
   * operators (< or >), indexed operators (<N or >N), comparison operators
   * (=, <>, <=, >=, <, >), or parts that pass padAtValid().
   *
   * @param string $part The name part to validate.
   *
   * @return bool TRUE if the part is valid, FALSE otherwise.
   */
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


  /**
   * Validates a comparison condition within an @ reference name part.
   *
   * Ensures that if a condition operator is present, both sides have
   * content and the left side passes padAtValid().
   *
   * @param string $part      The name part to check.
   * @param string $condition The condition operator to check for (=, <>, etc.).
   *
   * @return bool TRUE if no condition or valid condition, FALSE if invalid.
   */
  function padAtCheckCondition ( $part, $condition ) {

    if ( ! str_contains ( $part, $condition ) ) 
      return TRUE;

    $parts = explode ( $condition, $part );

    if ( count ( $parts ) <> 2       ) return FALSE;
    if ( ! strlen ( $parts [0] )     ) return FALSE;
    if ( ! strlen ( $parts [1] )     ) return FALSE;
    if ( ! padAtValid ( $parts [0] ) ) return FALSE;

    return TRUE;

  }


?>