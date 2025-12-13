<?php


  /**
   * Gets a field value with a namespace prefix.
   *
   * Searches for the field in the named scope (prefix), checking
   * level names, table data, and globals. Falls back to current
   * level or global scope based on idx parameter.
   *
   * @param string $field  The field name to find.
   * @param int    $idx    Level index for unprefixed search.
   * @param int    $type   The type of value expected (1-4).
   * @param string $prefix The namespace/scope prefix.
   *
   * @return mixed The field value or INF if not found.
   */
  function padFieldPrefix ( $field, $idx, $type, $prefix ) {

    if ( $prefix and ! is_numeric ( $prefix) ) {

      for ( $key = $GLOBALS ['pad']; $key >=0 ; $key-- ) {

        if ( $GLOBALS ['padName'] [$key] == $prefix)
          return padFieldSearch ( $GLOBALS ['padCurrent'] [$key], $field, $type );

        if ( isset ( $GLOBALS ['padTable'] [$key] [$prefix] ) )
          return padFieldSearch ( $GLOBALS ['padTable'] [$key] [$prefix], $field, $type );

      }

      if ( isset ( $GLOBALS [$prefix] ) )
        return padFieldSearch ( $GLOBALS [$prefix], $field, $type );
  
    }

    if ( $idx )
      return padFieldSearch ( $GLOBALS ['padCurrent'] [$idx], $field, $type );
    else
      return padFieldSearch ( $GLOBALS, $field, $type );

  }


  /**
   * Searches for a field within a data structure.
   *
   * Checks if field exists in the given array/object and matches
   * the expected type (scalar vs array).
   *
   * @param mixed  $current The data structure to search.
   * @param string $field   The field name to find.
   * @param int    $type    The type expected (1-2 scalar, 3-4 array).
   *
   * @return mixed The field value or INF if not found/wrong type.
   */
  function padFieldSearch ($current, $field, $type) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    if ( ! array_key_exists($field, $current ) )
      return INF;

    if ( ($type == 1 or $type == 2) and is_array($current[$field]) )
      return INF;

    if ( ($type == 3 or $type == 4) and ! is_array($current[$field]) )
      return INF;

    return $current [$field];

  }
  

?>