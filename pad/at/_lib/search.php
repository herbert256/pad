<?php

  // The path walker every at/ lookup ends in: given an array and the dot-separated $names
  // of an @ reference, return the value at that path or INF.
  //
  // padAtSearch tries the path at this level first (padAtSearchGo) and then, unless
  // $noDeep is set, recurses into every nested array whose key is a valid store name,
  // skipping pad* keys so the engine's own state is not walked. padAtSearchGo steps
  // through the path one name at a time, resolving an ordinal key via padAtKey, the *
  // wildcard via padAtSearchAny (a random element, or the first one whose remaining path
  // matches) and a comparison such as id=5 or name<>bob via padAtSearchCondition, which
  // returns the key of the first row satisfying it. padAtSearchIdx covers the positional
  // forms 3< (third from the start) and 2> (second from the end).

  function padAtSearch ( $current, $names, $noDeep = 0 ) {

    // There has to be something left to walk. padAtSearchAny recurses with whatever remains
    // of the path after a *, and that lands on a scalar as soon as the path is longer than
    // the data is deep - {$flat.*} ended the request on foreach() rather than answering.

    if ( is_object ( $current ) or is_resource ( $current ) )
      $current = (array) $current;

    if ( ! is_array ( $current ) )
      return INF;

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

    // $key is this name's position in the path and $found the array key it resolves to. They
    // used to share the one variable: padAtKey overwrote the position before padAtSearchAny
    // was handed it, and that function reads it as a position to work out what is left of the
    // path after the *. With the wrong value the remainder was wrong too, so every * either
    // walked into a scalar or came back not found.

    foreach ( $names as $key => $name ) {

      if ( is_object ($current) or is_resource ($current) )
        $current = (array) $current;

      $found = padAtKey ( $current, $name );

      if ( ! is_array ($current) or ! count ($current) )

        return INF;

      elseif ( $found ) {

        $current = &$current [$found];

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

      if     ( str_contains($name, '<>') ) { if ( $current [$key] [$before] != $after ) return $key; }
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