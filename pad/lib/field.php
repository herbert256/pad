<?php

  function pField_check ($parm) { return pField ($parm, 1); } 
  function pField_value ($parm) { return pField ($parm, 2); } 
  function pArray_check ($parm) { return pField ($parm, 3); } 
  function pArray_value ($parm) { return pField ($parm, 4); } 
  function pField_null  ($parm) { return pField ($parm, 5); } 

  function pField ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = pField_tag    ( $field        );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = pField_prefix ( $field, $type );
    else                                        $value = pField_level  ( $field, $type );

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }

  function pField_prefix ( $field, $type ) {

    global $p, $pCurrent;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pField_tag_nr ($prefix, $field);

    if ( $prefix == 'PHP' or $prefix === 1 or $prefix === '1' )
      return pField_search ($GLOBALS, $field, $type);

    $lvl = pField_tag_lvl_base ( $prefix, FALSE );

    if ( $lvl == 1 ) 
      $return = pField_search ($GLOBALS, $field, $type);
    elseif ( $lvl ) 
      $return = pField_search ($pCurrent [$lvl], $field, $type);

    if ( $return === INF ) {
      $GLOBALS['pField_double_check'] = TRUE;
      $tag = "$prefix#$field";
      if     ( pField_check($tag) ) $return = pField_value($tag);
      elseif ( pArray_check($tag) ) $return = pArray_value($tag);   
      $GLOBALS['pField_double_check'] = FALSE;
    }

    return $return;
    
  }


  function pField_level ( $field, $type ) {

    global $p, $pCurrent;

    if ( is_numeric($field) ) 
      return pField_tag_nr ('', $field);

    for ( $i=$p; $i; $i-- ) {

      if ( $i == 1 )
        $work = pField_search ( $GLOBALS, $field, $type );
      else
        $work = pField_search ( $pCurrent[$i], $field, $type );

      if ( $work !== INF )
        return $work;

    }

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

  function pFirst_non_parm  ($min=0) {

    global $p, $pType;

    for ($i=$p-$min; $i; $i--)
      if ( $pType[$i] <> 'parm' )
        return $i;

    if ( $p > 1 )
      return $p - 1;
    else
      return;

  }  


  function pField_tag_nr ($tag, $nr) {

    $lvl = pField_tag_lvl ($tag, FALSE);
    $idx = intval ($nr) - 1 ;

    global $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$lvl] [$idx] ) )
      return $pPrmsVal[$lvl] [$idx]; 
    else
      return INF;

  }
  

  function pField_tag ($field) {

    if ( substr($field, 0, 1) == '#' ) {
      $temp  = pExplode ($field, '#', 2);
      $tag   = '';
      $field = $temp[0];
      $parm  = $temp[1]??'';
    } else {
      $temp  = pExplode ($field, '#', 3);
      $tag   = $temp[0];
      $field = $temp[1];
      $parm  = $temp[2]??'';
    }

    if ( in_array ( $field, ['fields','names','values'] ) )
      $pIdx = pField_tag_lvl ($tag, TRUE);
    else
      $pIdx = pField_tag_lvl ($tag, FALSE);
    
    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $pIdx and isset($GLOBALS['pCurrent'] ) ) {
      $pos = 1;
      foreach( $GLOBALS['pCurrent'] [$pIdx] as $key => $value )
        if ( $pos++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    if ( $tag and ! $GLOBALS['pField_double_check'] ) {
      $chk = "$tag:$field";
      if ( pField_check($chk) ) return pField_value($chk);
      if ( pArray_check($chk) ) return pArray_value($chk);   
    }

    return INF;

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


?>