<?php


  function pField_prefix ( $field, $type ) {

    global $pad, $padCurrent, $padField_double_check;

    list ( $padrefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pField_prefix_nr ($padrefix, $field);

    $lvl = pFieldGetLevel ( $padrefix, FALSE );

    if ( $lvl === 0 )
      $return = pField_search ($GLOBALS, $field, $type);
    else 
      $return = pField_search ($padCurrent [$lvl], $field, $type);

    if ( $return === INF )
      $return = pFieldDoubleCheck ( $padrefix, '#', $field ); 

    return $return;
    
  }


  function pField_prefix_nr ($tag, $nr) {

    $lvl = pFieldGetLevel ($tag);
    $idx = intval ($nr) - 1 ;

    global $padPrmsVal;
    
    if ( isset ( $padPrmsVal[$lvl] [$idx] ) )
      return $padPrmsVal[$lvl] [$idx]; 
    else
      return INF;

  }


  function pField_search ($current, $field, $type) {

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