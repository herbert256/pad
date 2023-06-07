<?php


  function padAt ( $field ) {

    list ( $field, $kind ) = padSplit ( '@', $field );
    list ( $kind,  $name ) = padSplit ( ':', $kind  );

    $names = padExplode ( $field, '.' ); 

    if ( ! $kind or $kind == 'any' )
      return padAny ( $field, $names );

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padOpt, $padPrm, $padSetLvl;

    if ( padExists ( pad . "at/$kind.php" ) )
      return include pad . "at/$kind.php";

    return padAtName ( $kind, $names );

  }


  function padAtName ( $name, $names ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName, $padOpt, $padPrm, $padSetLvl;
 
    for ( $i=$pad; $i >=0; $i-- ) {

      if ( isset ( $padTable [$i] [$name] ) ) {
        $current = include pad . 'at/tbl.php';
        if ( $current !== INF ) 
          return $current;
      }

      if ( $padName [$i] == $name ) {
        $current = include pad . 'at/tag.php';
        if ( $current !== INF ) 
          return $current;
      }

    }

    if ( isset ( $padSeqStore [$name] ) ) {
      $current = padAtSearch ( $padSeqStore [$name], $names );
      if ( $current !== INF ) 
        return $current;
    }

    if ( isset ( $padDataStore [$name] ) ) {
      $current = padAtSearch ( $padDataStore [$name], $names );
      if ( $current !== INF ) 
        return $current;
    }
 
    if ( isset ( $GLOBALS [$name] ) ) {
      $current = padAtSearch ( $GLOBALS [$name], $names );
      if ( $current !== INF ) 
        return $current;
    }

    return INF;

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


?>