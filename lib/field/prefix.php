<?php


  function padFieldPrefix ( $field, $idx, $type ) {

    if ( $idx )
      return padFieldSearch ( $GLOBALS ['padCurrent'] [$idx], $field, $type );
    else 
      return padFieldSearch ( $GLOBALS, $field, $type );

  }


  function padFieldSearch ($current, $field, $type) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    $names = explode('.', $field);

    foreach ( $names as $name ) {

      if ( ! array_key_exists ( $name, $current ) )
        return INF;

      if ( is_object ($current[$name]) or is_resource ($current[$name]) )
        $current [$name] = (array) $current [$name];
         
       $current = &$current [$name];
        
    }

    if ( ($type == 1 or $type == 2) and is_array($current) )
      return INF;

    if ( ($type == 3 or $type == 4) and ! is_array($current) )
      return INF;

    return $current;

  }
  

?>