<?php

  // Resolves the field half of a prefix:field name against a single container, rather than
  // searching outwards the way padFieldLevel does.
  //
  // padFieldPrefix picks the container: a level whose name= matches the prefix supplies its
  // current iteration row, otherwise a global array of that name - not PHP's, nor the
  // engine's - is tried, otherwise the row of level $idx that padField already worked out
  // - or $GLOBALS at level 0.
  //
  // padFieldSearch does the actual read: objects and resources are cast to arrays first,
  // and a hit still has to match what the caller asked for - types 1 and 2 want a scalar,
  // 3 and 4 an array. A missing key or the wrong shape both give INF, the not-found marker.

  function padFieldPrefix ( $field, $idx, $type, $prefix ) {

    if ( $prefix and ! is_numeric ( $prefix) ) {

      for ( $key = $GLOBALS ['pad']; $key >=0 ; $key-- ) {

        if ( $GLOBALS ['padName'] [$key] == $prefix)
          return padFieldSearch ( $GLOBALS ['padCurrent'] [$key], $field, $type );

      }

      // An application's array, never PHP's own or the engine's: {$_SERVER:SERVER_SOFTWARE}
      // and {$_COOKIE:name} read what the field search no longer hands out (padFieldLevel).

      if ( isset ( $GLOBALS [$prefix] ) and ! padEngineName ( $prefix ) and ! str_starts_with ( $prefix, '_' ) )
        return padFieldSearch ( $GLOBALS [$prefix], $field, $type );

    }

    // A numeric prefix past either end of the level stack is a miss like any other.

    if ( $idx )
      return padFieldSearch ( $GLOBALS ['padCurrent'] [$idx] ?? [], $field, $type );
    else
      return padFieldSearch ( $GLOBALS, $field, $type );

  }

  function padFieldSearch ($current, $field, $type) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    // A prefix that names a scalar global holds no fields - not found, not a TypeError.

    if ( ! is_array ($current) or ! array_key_exists($field, $current ) )
      return INF;

    if ( ($type == 1 or $type == 2) and is_array($current[$field]) )
      return INF;

    if ( ($type == 3 or $type == 4) and ! is_array($current[$field]) )
      return INF;

    return $current [$field];

  }

?>
