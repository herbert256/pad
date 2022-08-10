<?php


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
  

  function pField_tag_lvl  ($search, $data) {

    global $p, $pName;

    for ( $i=$p; $i; $i-- )
      if ( $pName[$i] == $search )
        return $i;

    $return = pField_tag_lvl_base ($search, $data);
    
    if ( ! $return === FALSE)
      return $return;

    if ( isset( $GLOBALS['pDataStore'] [$search]) )
      return pField_fake_level ( $search, $GLOBALS['pDataStore'] );

    return $GLOBALS ['pad'];

  }  


  function pField_tag_lvl_base ($search, $data) {

    global $p, $pDefault, $pName, $pPrmsVal;

    if ( $data and ! isset($pPrmsVal [$p][0]) )
      for ($i=$p-1; $i; $i--)
        if ( !$pDefault[$i] )
          return $i;

    if ( trim($search) === '0' or trim($search) == '' )
      return $p;

    if ( is_numeric($search) and $search < 0 )
      return $p - abs($search);

    if ( is_numeric($search) ) 
      return $search;

    for ($i=$p; $i; $i--)
      if ( $pName[$i] == $search)
        return $i;

    return FALSE;

  }

  function pFieldDoubleCheck ($first, $seperator, $second) {

    $tag = "$first$seperator$second";
    
    if     ( pField_check($tag) ) return pField_value($tag);
    elseif ( pArray_check($tag) ) return pArray_value($tag); 

  }

?>