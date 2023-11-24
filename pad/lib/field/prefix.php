<?php


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