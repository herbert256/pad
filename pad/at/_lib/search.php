<?php


  /**
   * Searches an array structure for a value at the specified name path.
   *
   * First attempts a direct path search, then optionally performs a deep
   * recursive search through nested arrays (unless noDeep is set).
   *
   * @param array $current The array to search.
   * @param array $names   Array of name segments forming the lookup path.
   * @param int   $noDeep  If truthy, skip deep recursive search.
   *
   * @return mixed The found value, or INF if not found.
   */
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


  /**
   * Traverses an array following a path of name segments.
   *
   * Handles direct key lookups, wildcard (*) matching, comparison operators
   * (=, <>, <=, >=, <, >), and indexed access (<N, >N).
   *
   * @param array $current The array to traverse.
   * @param array $names   Array of name segments forming the lookup path.
   *
   * @return mixed The found value, or INF if path not found.
   */
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


  /**
   * Searches for a value in any element of an array (wildcard handler).
   *
   * When a wildcard (*) is encountered, searches all array elements
   * in random order for a matching value at the remaining path.
   *
   * @param int   $key     The current position in the names array.
   * @param array $current The array to search.
   * @param array $names   Array of name segments (includes wildcard and rest).
   *
   * @return mixed The found value, or INF if not found in any element.
   */
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


  /**
   * Finds an array element matching a comparison condition.
   *
   * Parses conditions like "field=value", "field<>value", etc. and returns
   * the key of the first matching element. Falls back to indexed search
   * if no comparison operator is present.
   *
   * @param array  $current The array to search.
   * @param string $name    The condition expression (e.g., "status=active").
   *
   * @return mixed The key of the matching element, or INF if none found.
   */
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
  

  /**
   * Gets an array element by positional index using directional operators.
   *
   * Handles "<N" (N-th from start, 1-based) and ">N" (N-th from end)
   * syntax for accessing array elements by position.
   *
   * @param array  $current The array to access.
   * @param string $name    The index expression (e.g., "<2" or ">1").
   *
   * @return mixed The key at the specified position, or INF if out of bounds.
   */
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