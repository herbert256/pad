<?php


  function padDot ( $field, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;

    $parts = padExplode ( $field, '.', 2 );
    $name  = $parts [0];

    if ( padExists ( pad . "dots/$name.php" )  ) {
      $field = $parts [1];
      return padDotPrefix ( $name, $field, $type );
    }
 
    for ( $i=$pad; $i >=0; $i-- ) {

      if ( isset ( $padCurrent [$i] [$name] ) ) {
        $current = padDotPrefix ( 'current', $field, $type );
        if ( $current !== INF ) 
          return $current;
      }

      if ( $padName [$i] == $name ) {
        $current = padDotPrefix ( 'tag', $field, $type );
        if ( $current !== INF ) 
          return $current;
      }

      if ( isset ( $padTable [$i] [$name] ) ) {
        $current = padDotPrefix ( 'table', $field, $type ); 
        if ( $current !== INF ) 
          return $current;
      }

    }

    if ( isset ( $padSeqStore [$name] ) ) {
      $current = padDotPrefix ( 'sequence', $field, $type );
      if ( $current !== INF ) 
        return $current;
    }

    if ( isset ( $padDataStore [$name] ) ) {
      $current =  padDotPrefix ( 'data', $field, $type );
      if ( $current !== INF ) 
        return $current;
    }
 
    if ( isset ( $GLOBALS [$name] ) ) {
      $current = padDotPrefix ( 'global', $field, $type ); 
      if ( $current !== INF ) 
        return $current;
    }

    return INF;
    
  }


  function padDotPrefix ( $prefix, $field, $type ) {

    global $pad, $padCurrent, $padTable, $padSeqStore, $padDataStore, $padName;
    global $padOpt, $padPrm, $padSetLvl;
    
    $names = padExplode ( $field, '.' ); 
    $name  = array_shift ($names);

    $first  = $names [0] ?? '';
    $second = $names [1] ?? '';
    $third  = $names [2] ?? '';

    return include pad . "dots/$prefix.php";
   
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


  function padDotGetValue ($current, $name) {

    if ( ! is_array ($current) or ! count ($current) )
      return INF;

    if ( array_key_exists( $name, $current) )
      return $current [$name];

    if ( $name == '<' )
      return $current [ array_key_first($current) ] ;

    if ( $name == '>' )
      return $current [ array_key_last($current) ] ;

    if ( $name == '*' )
      return $current [ array_rand ( $current ) ];

    if ( strpos($name, '<') !== FALSE or strpos($name, '>') !== FALSE  ) {
      $key = padDotGetIdx ($current, $name);
      if ( $key !== INF)
        return $current [$key];
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