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

    return padAt ( $names, $parts, $type );

  }


  function padAt ( $names, $parts, $type='var' ) {

    $parm = $field = $name = end ($names);

    $at = $parts [0];

    if ( count ( $parts ) == 2 ) $kind = $parts [1];
    else                         $kind = '';

    if     ( padIsTag   ( $at ) ) $padIdx = padIsTag   ( $at ); 
    elseif ( padIsLevel ( $at ) ) $padIdx = padIsLevel ( $at ); 
    else                          $padIdx = 0; 

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    if ( $padIdx ) 
      return include pad . 'at/tag.php';

    if ( file_exists ( "at/types/$at.php" ) ) 
      return include pad . "at/types/$at.php";

    return INF;

  }


  function padAtSearch ( $current, $names ) {

    foreach ( $names as $key => $name ) {

      if ( is_object ($current) or is_resource ($current) )
        $current = (array) $current;

      if ( ! is_array ($current) or ! count ($current) ) 

        return INF;

      elseif ( $name == '*' )
        
        return padAtSearchAny ( $key, $current, $names );

      elseif ( str_contains ($name, '<') or str_contains ($name, '>') or str_contains ($name, '=')  )  {
        
        $idx = padAtSearchCondition ( $current, $name );
        
        if ($idx === INF)
          return INF;
        
        $current = &$current [$idx];
        
        continue;

      } elseif ( ! array_key_exists ( $name, $current ) )
        
        return INF;

      $current = &$current [$name];
        
    }

    return $current;
    
  }


  function padAtSearchAny ( $key, $current, $names ) {

    $rest = [];
    foreach ( $names as $key2 => $name2 ) 
      if ( $key2 > $key)
        $rest [] = $name2;

    if ( ! count ($rest) ) 
      return $current [array_rand ($current)];

    $keys = array_keys ( $current );
    shuffle ( $keys );

    foreach ( $keys as $key ) {
      $work = padAtSearch ( $current [$key], $rest );
      if ( $work !== INF )
        return $work;
    }

    return INF;

  }


  function padAtSearchCondition ( $current, $name ) {

    if     ( str_contains($name, '<>') ) $parts = padExplode ( $name, '<>', 2 );
    elseif ( str_contains($name, '<=') ) $parts = padExplode ( $name, '<=', 2 );
    elseif ( str_contains($name, '>=') ) $parts = padExplode ( $name, '>=', 2 );
    elseif ( str_contains($name, '<')  ) $parts = padExplode ( $name, '<',  2 );
    elseif ( str_contains($name, '>')  ) $parts = padExplode ( $name, '>',  2 );
    elseif ( str_contains($name, '=')  ) $parts = padExplode ( $name, '=',  2 );

    if ( count($parts) < 2 )
      return padAtSearchIdx ( $current, $name );

    $before = $parts [0];
    $after  = padEval ( $parts [1] );

    foreach ( $current as $key => $value ) {

      if ( ! isset ( $current [$key] [$before] ) )
        continue;

      if     ( str_contains($name, '<>') ) { if ( $current [$key] [$before] <> $after ) return $key; }
      elseif ( str_contains($name, '<=') ) { if ( $current [$key] [$before] <= $after ) return $key; }
      elseif ( str_contains($name, '>=') ) { if ( $current [$key] [$before] >= $after ) return $key; }
      elseif ( str_contains($name, '<')  ) { if ( $current [$key] [$before] <  $after ) return $key; }
      elseif ( str_contains($name, '>')  ) { if ( $current [$key] [$before] >  $after ) return $key; }
      elseif ( str_contains($name, '=')  ) { if ( $current [$key] [$before] == $after ) return $key; }
    
    }

    return INF;

  }
  

  function padAtSearchIdx ( $current, $name ) {

    $start = ( str_contains ($name, '<') );
  
    $parts = ( $start ) ? padExplode ($name, '<') : padExplode ($name, '>');

    $key   = intval ($parts[0] ?? 1);
    $keys  = array_keys ( $current );
    $count = count ($keys);

    if ( $key < 1 or $key > $count )
      return INF;

    $idx = ( $start ) ? $key - 1 : count ($keys) - $key;

    return $keys [$idx];

  }


  function padFindNames ( $current, $names ) {

    $check = padAtSearch ( $current, $names );
    if ( $check !== INF)
      return $check;

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padFindNames ( $value, $names );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


  function padAtOneFind( $current, $one ) {

    if ( array_key_exists ( $one, $current) )
      return $current [$one];

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padAtOneFind ( $value, $one );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

  }


  function padIsTag ($name) {

    global $pad, $padName;

    for ( $i=$pad; $i; $i-- ) 
      if ( $padName [$i] == $name )
        return $i;

    return FALSE;

  }


  function padIsLevel ( $field ) {

    global $pad;

    if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
      $idx = $pad + $field;
      if ( $idx > 0 and $idx <= $pad )
        return $idx;
    }

    if ( is_numeric($field) ) 
      if ($field > 0 and $field <= $pad )
        return $field;

    return FALSE;

  }


  function padAtIdxNoName ( $start=0 ) {

    global $pad, $padName, $padType, $padTag;

    for ( $i=$pad-$start; $i; $i-- ) {

      if ( $padTag [$i] == 'if' or $padTag [$i] == 'case' )
        continue;

      if ( $padType[$i] == 'tag' )
        continue;

      if ( $padType[$i] == 'at' and file_exists ( pad . 'tag/' .  $padName[$i] . '.php' ) )
        continue;
      
      return $i;
    
    }
    
    return $pad;

  }


?>