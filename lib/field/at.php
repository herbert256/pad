<?php


  function padAt ( $field ) {

    global $pad, $padCurrent, $padData, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;

    list ( $field, $kind ) = padSplit ( '@', $field );
    list ( $kind,  $name ) = padSplit ( ':', $kind  );

    if ( ! $kind )
      $kind = 'any';

    $names = padExplode ( $field, '.' ); 

    if ( padExists ( pad . "var/at/$kind.php" ) )
      return include pad . "var/at/$kind.php";

    $name = $kind;
    
    return include pad . 'var/name.php';

  }


  function padAtSearch ( $current, $names ) {

    foreach ( $names as $key => $name ) {

      if ( is_object ($current) or is_resource ($current) )
        $current = (array) $current;

      if ( ! is_array ($current) or ! count ($current) ) 
        return INF;

      if ( $name == '*' )
        return padAtAny ( $key, $current, $names );

      if ( str_contains ($name, '<') or str_contains ($name, '>')  )  {
        $idx = padAtGetIdx($current, $name);
        if ($idx === INF)
          return INF;
        $current = &$current [$idx];
        continue;
      }

      if ( ! array_key_exists ( $name, $current ) )
        return INF;

      $current = &$current [$name];
        
    }

    if ( is_array ($current) ) 
      return INF;

    return $current;
    
  }


  function padAtAny ( $key, $current, $names ) {

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


  function padAtGetIdx ($current, $name ) {

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


  function padAtNamesFind ( $current, $names ) {

    $check = padAtSearch ( $current, $names );
    if ( $check !== INF)
      return $check;

    foreach ( $current as $key => $value ) {

      if ( is_object ($value) or is_resource ($value) )
        $value = (array) $value;

      if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
        $check = padAtNamesFind ( $value, $names );
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


?>