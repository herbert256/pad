<?php


  function padAtSearch ( $current, $names, $noDeep = 0 ) {

    $check = padAtSearchGo ( $current, $names );
    if ( $check !== INF)
      return $check;

    if ( $noDeep )
      return INF;
    
    foreach ( $current as $key => $value ) 

      if ( padValidStore ($key) ) {

        if ( is_object ($value) or is_resource ($value) )
          $value = (array) $value;

        if ( is_array ($value) and ! str_starts_with ($key, 'pad') ) {
          $check = padAtSearch ( $value, $names );
          if ( $check !== INF )
            return $check;
        }

      }

    return INF;

  }


  function padAtSearchGo ( $current, $names ) {

    foreach ( $names as $key => $name ) {

      if ( is_object ($current) or is_resource ($current) )
        $current = (array) $current;

      $key = padAtKey ( $current, $name );

      if ( ! is_array ($current) or ! count ($current) ) 

        return INF;

      elseif ( $key ) {

        $current = &$current [$key];
        
        continue;

      } elseif ( $name == '*' )
        
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


?>