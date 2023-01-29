<?php


  function padFieldPrefix ( $field, $type ) {

    global $pad, $padCurrent, $padFieldDoubleCheck;

    list ( $padrefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return padFieldPrefixNr ($padrefix, $field);

    $lvl = padFieldGetLevel ( $padrefix, FALSE );

    if ( $lvl === 0 )
      $return = padFieldSearch ($GLOBALS, $field, $type);
    else 
      $return = padFieldSearch ($padCurrent [$lvl], $field, $type);

    if ( $return === INF )
      $return = padFieldDoubleCheck ( $padrefix, '#', $field ); 

    return $return;
    
  }


  function padFieldPrefixNr ($tag, $nr) {

    $lvl = padFieldGetLevel ($tag);
    $idx = intval ($nr) - 1 ;

    global $padPrm;
    
    if ( isset ( $padPrm [$lvl] [$idx] ) )
      return $padPrm [$lvl] [$idx]; 
    else
      return INF;

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