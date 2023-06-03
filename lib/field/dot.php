<?php

  function padDot ( $field, $type ) {

    global $pad;

    $names = padExplode ( $field, ':' , 2);

    if ( count ($names) == 2 ) {
      $todo = TRUE;
      // sequence / table / global / current / data
    }

    $names = padExplode ( $field, '.' );
    $check = $names[0] ?? '';

    if ( count ($names) == 2 )
      if ( isset( $GLOBALS['padSeqStore'] [$check] ) {
        if ( strpos($name, '<') !== FALSE or strpos($name, '>') !== FALSE or $name == '*' )  {
          $store = $names[0];
          unset ($names[0]);
          return padDotSearch ( $GLOBALS['padSeqStore'] [$store], $names, $type)
        }

    if ( count ( $names ) == 1 )
      return INF;
 
    $first = array_shift($names);

    for ( $i=$pad; $i; $i-- ) {

      if ( isset ( $padCurrent [$i] [$first] ) ) {
        $current = padDotSearch ( $padCurrent [$i] [$first], $names, $type ); 
        if ( $current !== INF ) 
          return $current;
      }

      if ( isset ( $padTable [$i] [$first] ) ) {
        $current = padDotSearch ( $padTable [$i] [$first], $names, $type ); 
        if ( $current !== INF ) 
          return $current;
      }

    }

    if ( isset ( $GLOBALS [$first] ) ) {
      $current = padDotSearch ( $GLOBALS [$first], $names, $type ); 
      if ( $current !== INF ) 
        return $current;
    }

    return INF;
    
  }


  function padDotGetIdx ($current, $name) {

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


  function padDotSearch ( $current, $names, $type) {
  
    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) ) 
      return INF;

    foreach ( $names as $key => $name ) {

      if ( $name == '*')
        return padDotAny ( $key, $current, $type, $names );

     if ( $name == '<') {
        $current = &$current [array_key_first($current)];
        continue;
      }

      if ( $name == '>') {
        $current = &$current [array_key_last($current)];
        continue;
      }

      if ( strpos($name, '<') !== FALSE  or strpos($name, '>') !== FALSE )  {
        $idx = padDotGetIdx($current, $name);
        if ($idx === INF)
          return INF;
        $current = &$current [$idx];
        continue;
      }

      if ( ! array_key_exists ( $name, $current ) )
        return INF;

      if ( is_object ($current[$name]) or is_resource ($current[$name]) )
        $current[$name] = (array) $current[$name];
      
      $current = &$current [$name];
        
    }

    return padDotReturn ( $current, $type );
    
  }


  function padDotAny ( $key, $current, $type, $names ) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! is_array ($current) or ! count ($current) ) 
      return INF;

    $rest = [];
    foreach ( $names as $key2 => $name2 ) 
      if ( $key2 > $key)
        $rest [] = $name2;

    if ( ! count ($rest) ) 
      return padDotReturn ( padDotRandom ( $current), $type );

    $keys = array_keys ( $current );
    shuffle ( $keys );

    foreach ( $keys as $key ) {
      $work = padDotSearch ( $current [$key], $rest, $type );
      if ( $work !== INF )
        return $work;
    }

    return INF;


  }


  function padDotReturn ( $current, $type ) {

    if     ($type == 9 and ! is_array ( $current ) and $current === NULL ) return NULL;
    if     (   is_array ( $current ) and ( $type == 3 or $type == 4 ) ) return $current;
    elseif ( ! is_array ( $current ) and ( $type == 1 or $type == 2 ) ) return $current;

    return INF;

  } 


  function padDotRandom ( $array ) {

    $work = array_rand ($array );

    return $array [ $work ];

  }


?>