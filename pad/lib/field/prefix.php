<?php


  function pField_prefix ( $field, $type ) {

    global $p, $pCurrent, $pField_double_check;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pField_prefix_nr ($prefix, $field);

    $lvl = pFieldGetLevel ( $prefix, FALSE );

    if ( $lvl === 0 )
      $return = pField_search ($GLOBALS, $field, $type);
    else 
      $return = pField_search ($pCurrent [$lvl], $field, $type);

    if ( $return === INF )
      $return = pFieldDoubleCheck ( $prefix, '#', $field ); 

    return $return;
    
  }


  function pField_prefix_nr ($tag, $nr) {

    $lvl = pFieldGetLevel ($tag);
    $idx = intval ($nr) - 1 ;

    global $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$lvl] [$idx] ) )
      return $pPrmsVal[$lvl] [$idx]; 
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