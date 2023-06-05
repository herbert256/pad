<?php


  function padDotReturn ( $work, $type ) {

    if     ( $work === INF                                          ) return INF;
    elseif ( $type == 9 and ! is_array ( $work ) and $work === NULL ) return NULL;
    elseif (   is_array ( $work ) and ( $type == 3 or $type == 4 )  ) return $work;
    elseif ( ! is_array ( $work ) and ( $type == 1 or $type == 2 )  ) return $work;
    else                                                              return INF;
    
  }

  function padDot ( $field, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName;

    list ( $field, $kind ) = padSplit ( '@', $field );
    list ( $kind,  $name ) = padSplit ( ':', $kind  );

    $names = padExplode ( $field, '.' ); 

    if ( $kind ) {

      if ( padExists ( pad . "dots/$kind.php" ) )
        return padDotKind ( $kind, $name, $names, $type );

      $name = $kind;
 
    }

    $names = padExplode ( $field, '.' ); 

    if ( ! $name )
      return padDotPlain ( $names, $type );
 
    for ( $i=$pad; $i >=0; $i-- ) {

      if ( isset ( $padCurrent [$i] [$name] ) ) {
        $current = padDotKind ( 'current', $name, $names, $type );
        if ( $current !== INF ) 
          return $current;
      }

      if ( $padName [$i] == $name ) {
        $current = padDotKind ( 'tag', $name, $names, $type );
        if ( $current !== INF ) 
          return $current;
      }

      if ( isset ( $padTable [$i] [$name] ) ) {
        $current = padDotKind ( 'table', $name, $names, $type );
        if ( $current !== INF ) 
          return $current;
      }

    }

    if ( isset ( $padSeqStore [$name] ) ) {
      $current = padDotKind ( 'sequence', $name, $names, $type );
      if ( $current !== INF ) 
        return $current;
    }

    if ( isset ( $padDataStore [$name] ) ) {
      $current =  padDotKind ( 'data', $name, $names, $type );
      if ( $current !== INF ) 
        return $current;
    }
 
    if ( isset ( $GLOBALS [$name] ) ) {
      $current = padDotKind ( 'global', $name, $names, $type );
      if ( $current !== INF ) 
        return $current;
    }

    $names = padExplode ( "$name.$field", '.' ); 

    $current = padDotPlain ( $names, $type );
    if ( $current !== INF ) 
      return $current;

    return INF;

  }


  function padDotPlain ( $names, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padData;
    global $padOpt, $padPrm, $padSetLvl;

    for ( $i=$pad; $i >= 0; $i-- ) {

      $current = include pad . "dots/go/tag.php";
      
      if ( $current !== INF ) 
        return  $current;

    }

    $current = padDotSearch ( $padDataStore, $names, $type ); 
    if ( $current !== INF ) 
      return $current;
  
    $current = padDotSearch ( $padSeqStore, $names, $type ); 
    if ( $current !== INF ) 
      return $current;
  
    $current = padDotSearch ( $padData, $names, $type ); 
    if ( $current !== INF ) 
      return $current;

    $current = padDotSearch ( $GLOBALS, $names, $type ); 
    if ( $current !== INF ) 
      return $current;

    $current = padDotFind ( $GLOBALS, $names, $type ); 
    if ( $current !== INF ) 
      return $current;
  
    return INF;

  }


  function padDotFind ( $current, $names, $type ) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) ) 
      return INF;

    foreach ( $current as $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) ) {

        $current = padDotSearch ( $value, $names, $type );
        if ( $current !== INF ) 
          return $current;

        $current = padDotFind ( $value, $names, $type );
        if ( $current !== INF ) 
          return $current;

      }

    }

    return INF;
    
  }

  function padDotKind ( $kind, $name, $names, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;

    $first  = $names [0] ?? '';
    $second = $names [1] ?? '';
    $third  = $names [2] ?? '';

    $current = include pad . "dots/$kind.php";
   
    return padDotReturn ( $current, $type );
  
  }


  function padDotSearch ( $current, $names, $type ) {
  
    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) ) 
      return INF;

    foreach ( $names as $key => $name ) {

      if ( $name == '*' )
        return padDotAny ( $key, $current, $names, $type );

     if ( $name == '<')  {
        $current = &$current [array_key_first($current)];
        continue;
      }

      if ( $name == '>' ) {
        $current = &$current [array_key_last($current)];
        continue;
      }

      if ( strpos($name, '<') !== FALSE  or strpos($name, '>') !== FALSE )  {
        $idx = padDotGetIdx($current, $name, $type);
        if ($idx === INF)
          return INF;
        $current = &$current [$idx];
        continue;
      }

      if ( ! is_array ($current) or ! array_key_exists ( $name, $current ) )
        return INF;

      if ( is_object ($current[$name]) or is_resource ($current[$name]) )
        $current[$name] = (array) $current[$name];
      
      $current = &$current [$name];
        
    }

    return padDotReturn ( $current, $type );
    
  }


  function padDotAny ( $key, $current, $names, $type ) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) or ! count ($current) ) 
      return INF;

    $rest = [];
    foreach ( $names as $key2 => $name2 ) 
      if ( $key2 > $key)
        $rest [] = $name2;

    if ( ! count ($rest) ) 
      return padDotReturn ( $current [array_rand ($current)] , $type);

    $keys = array_keys ( $current );
    shuffle ( $keys );

    foreach ( $keys as $key ) {
      $work = padDotSearch ( $current [$key], $rest, $type );
      if ( $work !== INF )
        return $work;
    }

    return INF;

  }


  function padDotGetIdx ($current, $name, $type ) {

    $start = ( strpos($name, '<') !== FALSE );
  
    if ( $start )
      $parts = padExplode ($name, '<');
    else
      $parts = padExplode ($name, '>');

    $key   = intval ($parts[0] ?? 1);
    $keys  = array_keys ( $current );
    $count = count ($keys);

    if ( $key < 1 or $key > $count )
      return INF;

    if ( $start )
      $idx = $key - 1;
    else
      $idx = count ($keys) - $key;

    return $keys [$idx];

  }


?>