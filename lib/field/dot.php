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

    if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
      $idx = $pad + $field;
      if ( $type == 1 and $idx and isset ($padCurrent [$idx]) )
        return TRUE;
      if ( $type == 2 and $idx and isset ($padCurrent [$idx]) and is_array ($padCurrent [$idx]) )
        foreach ($padCurrent [$idx] as $value)
          if ( is_scalar($value) )
            return $value;
    }

    if ( is_numeric($field) ) 
      if ( array_key_exists ( $field, $padOpt [$pad] ) )
        return $padOpt [$pad] [$field];

    list ( $field, $kind ) = padSplit ( '@', $field );
    list ( $kind,  $name ) = padSplit ( ':', $kind  );

    $names = padExplode ( $field, '.' ); 

    if ( $kind ) {

      if ( padExists ( pad . "dots/$kind.php" ) )
        return padDotKind ( $kind, $name, $names, $type );

      $name = $kind;
 
    }

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


  function padDotKind ( $kind, $name, $names, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;

    $current = include pad . "dots/$kind.php";
   
    return padDotReturn ( $current, $type );
  
  }


  function padDotPlain ( $names, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padData;
    global $padOpt, $padPrm, $padSetLvl;

    for ( $i=$pad; $i >= 0; $i-- ) {

      $current = padDotSearch ( $padCurrent [$i], $names, $type ); 
      if ( $current !== INF ) 
        return $current;

      $current = padDotSearch ( $padTable [$i], $names, $type ); 
      if ( $current !== INF ) 
        return $current;

    }

    $current = padDotSearch ( $GLOBALS, $names, $type ); 
    if ( $current !== INF ) 
      return $current;

    for ( $i=$pad; $i >= 0; $i-- ) {

      $padOptDot = $padOpt [$i];
      unset ( $padOptDot [0] );

      $current = padDotSearch ( $padData [$i], $names, $type ); 
      if ( $current !== INF ) 
        return $current;

      $current = padDotSearch ( $padOptDot, $names, $type ); 
      if ( $current !== INF ) 
        return $current;

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

    for ( $i=$pad; $i >= 0; $i-- ) {

      $current = padDotSearch ( $padPrm [$i], $names, $type ); 
      if ( $current !== INF ) 
        return $current;

    }


    return padDotFind ( $GLOBALS, $names, $type ); 
  
  }


  function padDotFind ( $current, $names, $type ) {

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


  function padDotSearch ( $current, $names, $type ) {

    foreach ( $names as $key => $name ) {

      $current = padDotCheck ($current);
      if ( $current === INF )
        return INF;

      if ( $name == '*' )
        return padDotAny ( $key, $current, $names, $type );

      if ( str_contains ($name, '<') or str_contains ($name, '>')  )  {
        $idx = padDotGetIdx($current, $name, $type);
        if ($idx === INF)
          return INF;
        $current = &$current [$idx];
        continue;
      }

      if ( ! array_key_exists ( $name, $current ) )
        return INF;

      $current = &$current [$name];
        
    }

    return padDotReturn ( $current, $type );
    
  }


  function padDotAny ( $key, $current, $names, $type ) {

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


  function padDotCheck ($current) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) or ! count ($current) ) 
      return INF;

    return $current;

  }


?>