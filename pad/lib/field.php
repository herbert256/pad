<?php

  function pad_field_check ($parm) { return pad_field ($parm, 1); } 
  function pad_field_value ($parm) { return pad_field ($parm, 2); } 
  function pad_array_check ($parm) { return pad_field ($parm, 3); } 
  function pad_array_value ($parm) { return pad_field ($parm, 4); } 
  function pad_field_null  ($parm) { return pad_field ($parm, 5); } 

  function pad_field ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = pad_field_tag    ( $field        );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = pad_field_prefix ( $field, $type );
    else                                        $value = pad_field_level  ( $field, $type );

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }

  function pad_field_prefix ( $field, $type ) {

    global $pad_lvl, $pad_db_lvl, $pad_current;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pad_field_tag_nr ($prefix, $field);

    if ( $prefix == 'PHP' or $prefix === 1 or $prefix === '1' )
      return pad_field_search ($GLOBALS, $field, $type);

    $lvl = pad_field_tag_lvl_base ( $prefix, FALSE );

    if ( $lvl == 1 ) 
      $return = pad_field_search ($GLOBALS, $field, $type);
    elseif ( $lvl ) 
      $return = pad_field_search ($pad_current [$lvl], $field, $type);
    else
      for ( $i=$pad_lvl; $i; $i-- )
        foreach ( $pad_db_lvl [$i] as $key => $value)
          if ($key == $prefix)
            $return = pad_field_search ($value, $field, $type);

    if ( $return === INF ) {
      $GLOBALS['pad_field_double_check'] = TRUE;
      $tag = "$prefix#$field";
      if     ( pad_field_check($tag) ) $return = pad_field_value($tag);
      elseif ( pad_array_check($tag) ) $return = pad_array_value($tag);   
      $GLOBALS['pad_field_double_check'] = FALSE;
    }

    return $return;
    
  }


  function pad_field_level ( $field, $type ) {

    global $pad_lvl, $pad_db_lvl, $pad_current, $pad_parameters;

    if ( is_numeric($field) ) 
      return pad_field_tag_nr ('', $field);

    for ( $i=$pad_lvl; $i; $i-- ) {

      if ( $i == 1 )
        $work = pad_field_search ( $GLOBALS, $field, $type );
      else
        $work = pad_field_search ( $pad_current[$i], $field, $type );

      if ( $work !== INF )
        return $work;

      foreach ( $pad_db_lvl [$i] as $key => $value ) {
        $work = pad_field_search ( $value, $field, $type);   
        if ( $work !== INF )
          return $work;

      $work = pad_field_search ( $pad_parameters [$i] ['parms_tag'], $field, $type);   
      if ( $work !== INF )
        return $work;

      }

    }

    return INF;
    
  }


  function pad_field_search ($current, $field, $type) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    $names = explode('.', $field);

    foreach ( $names as $name ) {

      if ( ! array_key_exists ( $name, $current ) )
        return INF;

      if ( is_object ($current[$name]) or is_resource ($current[$name]) )
        $current[$name] = (array) $current[$name];
          
      $current = &$current [$name];
        
    }

    if ( ($type == 1 or $type == 2) and is_array($current) )
      return INF;

    if ( ($type == 3 or $type == 4) and ! is_array($current) )
      return INF;

    return $current;

  }

  function pad_first_non_parm  ($min=0) {

    global $pad_lvl, $pad_parameters;

    for ($i=$pad_lvl-$min; $i; $i--)
      if ( $pad_parameters [$i] ['tag_type'] <> 'parm' )
        return $i;

    if ( $pad_lvl > 1 )
      return $pad_lvl - 1;
    else
      return;

  }  


  function pad_field_tag_nr ($tag, $nr) {

    $lvl = pad_field_tag_lvl ($tag, FALSE);
    $idx = intval ($nr) - 1 ;

    global $pad_parameters;
    
    if ( isset ( $pad_parameters [$lvl] ['parms_val'] [$idx] ) )
      return $pad_parameters [$lvl] ['parms_val'] [$idx]; 
    else
      return INF;

  }
  

  function pad_field_tag ($field) {

    if ( substr($field, 0, 1) == '#' ) {
      $temp  = pad_explode ($field, '#', 2);
      $tag   = '';
      $field = $temp[0];
      $parm  = $temp[1]??'';
    } else {
      $temp  = pad_explode ($field, '#', 3);
      $tag   = $temp[0];
      $field = $temp[1];
      $parm  = $temp[2]??'';
    }

    if ( in_array ( $field, ['fields','names','values'] ) )
      $pad_idx = pad_field_tag_lvl ($tag, TRUE);
    else
      $pad_idx = pad_field_tag_lvl ($tag, FALSE);
    
    if ( file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $pad_idx and isset($GLOBALS['pad_current'] ) ) {
      $pos = 1;
      foreach( $GLOBALS['pad_current'] [$pad_idx] as $key => $value )
        if ( $pos++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    if ( $tag and ! $GLOBALS['pad_field_double_check'] ) {
      $chk = "$tag:$field";
      if ( pad_field_check($chk) ) return pad_field_value($chk);
      if ( pad_array_check($chk) ) return pad_array_value($chk);   
    }

    return INF;

  }


  function pad_field_tag_lvl  ($search, $data) {

    global $pad_lvl, $pad_parameters;

    for ($i=$pad_lvl; $i; $i--)
      if ( $pad_parameters [$i] ['name'] == $search )
        return $i;

    $return = pad_field_tag_lvl_base ($search, $data);
    
    if ( ! $return === FALSE)
      return $return;

    if ( isset( $GLOBALS['pad_data_store'] [$search]) )
      return pad_field_fake_level ( $search, $GLOBALS['pad_data_store'] );

    for ($i=$pad_lvl; $i; $i--)
      if ( isset( $GLOBALS['pad_db_lvl'] [$i] ) )
        if ( isset( $GLOBALS['pad_db_lvl'] [$i] [$search]) )
          return pad_field_fake_level ( $search, $GLOBALS['pad_db_lvl'] [$i] [$search] );

    return $GLOBALS ['pad_lvl'];

  }  


  function pad_field_tag_lvl_base ($search, $data) {

    global $pad_lvl, $pad_parameters, $pad_parms_val;

    if ( $data and ! isset($pad_parms_val[0]) )
      for ($i=$pad_lvl-1; $i; $i--)
        if ( ! $pad_parameters [$i] ['default_data'] )
          return $i;

    if ( trim($search) === '0' or trim($search) == '' )
      return $pad_lvl;

    if ( is_numeric($search) and $search < 0 )
      return $pad_lvl - abs($search);

    if ( is_numeric($search) ) 
      return $search;

    for ($i=$pad_lvl; $i; $i--)
      if ( $pad_parameters [$i] ['name'] == $search)
        return $i;

    return FALSE;

  }


  function pad_field_fake_level ( $name, $data ) {

    global $pad_lvl;

    $pad_lvl_save = $pad_lvl;

    $pad_lvl = 999999;
    include PAD . 'inits/level.php';
  
    $GLOBALS ['pad_data']    [$pad_lvl] = pad_make_data ( $fake );
    $GLOBALS ['pad_current'] [$pad_lvl] = reset ( $GLOBALS ['pad_data'] );
    $GLOBALS ['pad_key']     [$pad_key] = key ( $GLOBALS ['pad_data'] [$pad_lvl] );
    $GLOBALS ['pad_occur']   [$pad_key] = 1;

    $pad_lvl = $pad_lvl_save;

    return $pad_lvl;
    
  }


?>