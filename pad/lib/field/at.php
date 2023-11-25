<?php


  function padAt ( $field, $type ) {

    global $pad, $padCurrent, $padData, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;

    padAtSet ( $field, $kind, $name, $property );

    $names = padExplode ( $field, '.' ); 

    if     ( count ($names) ) $forceName = end ($names);
    elseif ( $property )      $forceName = $property;
    else                      $forceName = $name;

    $GLOBALS ['padForceTagName']  = $forceName;
    $GLOBALS ['padForceDataName'] = $forceName;

    if     ( $name and padIsTag   ($name) ) $i = padIsTag       ($name); 
    elseif ( $name and padIsLevel ($name) ) $i = padIsLevel     ($name); 
    elseif ( $type == 'tag'               ) $i = padAtIdxNoName (1);
    else                                    $i = padAtIdxNoName (0);

    $first  = $names [0] ?? '';
    $second = $names [1] ?? '';

    if ( padXref ) 
      include pad . 'tail/types/xref/items/at.php';

    if     ( $property )  return include pad . "var/property.php";
    elseif ( $kind     )  return include pad . "var/at/$kind.php";
    else                  return include pad . 'var/name.php'; 

  }


  function padAtSet ( &$field, &$kind, &$name, &$property ) {

    list ( $field, $after ) = padSplit ( '@', $field );
    list ( $first, $second) = padSplit ( '.', $after );
    
    $kind     = 'any';
    $name     = $first;
    $property = '';
  
    if ( $second and padExists ( pad . "var/at/$second.php" ) )
 
      $kind = $second;
   
    elseif ( $second and padExists ( pad . "tag/$second.php" ) )

      $property = $second;

    elseif ( $first and ! $second ) {
  
      if ( padIsTag ($first) or padIsLevel ($first) ) {

        $kind = 'tag';

      } elseif ( padExists ( pad . "var/at/$first.php" ) ) {

        $kind = $first;
        $name = '';            

      } elseif ( padExists ( pad . "tag/$first.php") ) {

        $property = $first;
        $name     = '';            

      } else {

        $kind = '';
        
      }

    }
        
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


  function padFindName ( $current, $name, $names ) {

    if ( array_key_exists ($name, $current) ) {
      $check = padAtSearch ( $current [$name], $names );
      if ( $check !== INF)
        return $check;
    }

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padFindName ( $value, $name, $names );
        if ( $check !== INF )
          return $check;
      }

    }

    return INF;

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

      if ( $padType[$i] == 'at' and padExists ( pad . 'tag/' .  $padName[$i] . '.php' ) )
        continue;
      
      return $i;
    
    }
    
    return $pad;

  }


?>