<?php


  function padFieldPrefix ( $field, $type, $prefix, $lvl ) {

    global $pad, $padPrm, $padCurrent;

    if ( is_numeric($field) ) {

      $lvl = padFieldGetLevel ($prefix);
      $idx = intval ($field) - 1 ;

      if ( isset ( $padPrm [$lvl] [$idx] ) )
        return $padPrm [$lvl] [$idx]; 
      else
        return INF;
    }

    if ( $lvl === 0 )
      return padFieldSearch ($GLOBALS, $field, $type);
    else 
      return padFieldSearch ($padCurrent [$lvl], $field, $type);

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