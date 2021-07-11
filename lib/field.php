<?php

  define ( 'PAD_NOT_FOUND', '*pad*Floe%pende!Flap*pad*' );

  function pad_field_check ($parm) { return pad_field ($parm, 1); } 
  function pad_field_value ($parm) { return pad_field ($parm, 2); } 
  function pad_array_check ($parm) { return pad_field ($parm, 3); } 
  function pad_array_value ($parm) { return pad_field ($parm, 4); } 

  function pad_field ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = pad_field_tag    ( $field        );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = pad_field_prefix ( $field, $type );
    else                                        $value = pad_field_level  ( $field, $type );

    if      ($type == 1) return ( $value !== NULL and ( $value === PAD_NOT_FOUND or ! is_scalar($value) ) ) ? FALSE : TRUE;
    else if ($type == 2) return ( $value === NULL or    $value === PAD_NOT_FOUND or ! is_scalar($value)   ) ? ''    : $value;
    else if ($type == 3) return ( $value === NULL or    $value === PAD_NOT_FOUND or   is_scalar($value)   ) ? FALSE : TRUE;
    else                 return ( $value === NULL or    $value === PAD_NOT_FOUND or   is_scalar($value)   ) ? []    : $value;

  }
  
  function pad_field_tag_parm ($tag, $field) {

    $lvl = pad_field_tag_lvl ($tag);
    $idx = intval ($field) - 1 ;

    global $pad_lvl, $pad_parameters;
    
    if ( isset ( $pad_parameters [$lvl] ['parms_seq'] [$idx] ) )
      return $pad_parameters [$lvl] ['parms_seq'] [$idx]; 
    else
      return PAD_NOT_FOUND;

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

    if ( is_numeric($field) )
      return pad_field_tag_parm ($tag, $field);

    $pad_idx = pad_field_tag_lvl ($tag);
    
    if ( pad_file_exists ( PAD_HOME . "tag/".$field.".php" ) )
      return include PAD_HOME . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $pad_idx and isset($GLOBALS['pad_current'] ) ) {
      $pos = 1;
      foreach( $GLOBALS['pad_current'] [$pad_idx] as $key => $value )
        if ( $pos++ == $field )
          return  ( $parm == 'name') ? $key : $value;
    }

    return PAD_NOT_FOUND;

  }

  function pad_field_prefix ( $field, $type ) {

    global $pad_lvl, $pad_db_lvl, $pad_current;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pad_field_tag_parm ($prefix, $field);

    if ( $prefix == 'PHP' or $prefix === 1 or $prefix === '1' )
      return pad_field_search ($GLOBALS, $field, $type);

    $lvl = pad_field_tag_lvl_base ( $prefix );

    if ( $lvl == 1 ) 
      return pad_field_search ($GLOBALS, $field, $type);
    elseif ( $lvl ) 
      return pad_field_search ($pad_current [$lvl], $field, $type);
    else
      for ( $i=$pad_lvl; $i; $i-- )
        foreach ( $pad_db_lvl [$i] as $key => $value)
          if ($key == $prefix)
            return pad_field_search ($value, $field, $type);

    return PAD_NOT_FOUND;
    
  }


  function pad_field_level ( $field, $type ) {

    global $pad_lvl, $pad_db_lvl, $pad_current;

    if ( is_numeric($field) ) 
      return pad_field_tag_parm ('', $field);

    for ( $i=$pad_lvl; $i; $i-- ) {

      if ( $i == 1 )
        $work = pad_field_search ( $GLOBALS, $field, $type );
      else
        $work = pad_field_search ( $pad_current[$i], $field, $type );

      if ( $work !== PAD_NOT_FOUND )
        return $work;

      foreach ( $pad_db_lvl [$i] as $key => $value ) {
        $work = pad_field_search ( $value, $field, $type);   
        if ( $work !== PAD_NOT_FOUND )
          return $work;
      }

    }

    return PAD_NOT_FOUND;
    
  }


  function pad_field_search ($current, $field, $type) {

    if ( is_object ($current) or is_resource ($current) )
      $current = (array) $current;

    $names = explode('.', $field);

    foreach ( $names as $name ) {

      if ( ! array_key_exists ( $name, $current ) )
        return PAD_NOT_FOUND;

      if ( is_object ($current[$name]) or is_resource ($current[$name]) )
        $current[$name] = (array) $current[$name];
          
      $current = &$current [$name];
        
    }

    if ( ($type == 1 or $type == 2) and is_array($current) )
      return PAD_NOT_FOUND;

    if ( ($type == 3 or $type == 4) and ! is_array($current) )
      return PAD_NOT_FOUND;

    return $current;

  }


  function pad_field_tag_lvl_base ($search = '') {

    global $pad_lvl, $pad_parameters;

    if ($search=='')
      return $pad_lvl;

    $find = (int) $search;
 
    if ( $find < 0 ) 
      return $pad_lvl + $find;

    if ( is_numeric($search) ) 
      return (int) $search;

    for ($i=$pad_lvl; $i; $i--)
      if ( isset($pad_parameters [$i] ['name']) and $pad_parameters [$i] ['name'] == $search)
        return $i;

    return FALSE;

  }


  function pad_field_tag_lvl  ($search = '') {

    $return = pad_field_tag_lvl_base ($search);
    if ( $return )
      return $return;

    global $pad_lvl, $pad_parameters;

    if ( isset( $GLOBALS['pad_data_store'] [$search]) )
      return pad_field_tag_lvl_fake ( $GLOBALS['pad_data_store'], $search );

    if ( isset( $GLOBALS['pad_seq_store'] [$search]) )
      return pad_field_tag_lvl_fake ( $GLOBALS['pad_seq_store'], $search );

    for ($i=$pad_lvl; $i; $i--)
      if ( isset( $GLOBALS['pad_db_lvl'] [$i] ) )
        if ( isset( $GLOBALS['pad_db_lvl'] [$i] [$search]) )
          return pad_field_tag_lvl_fake ( $GLOBALS['pad_db_lvl'] [$i] [$search], $search );

    return $GLOBALS ['pad_lvl'];

  }  


  function pad_field_tag_lvl_fake ($fake, $name) {

    global $pad_lvl;

    $pad_lvl_save = $pad_lvl;

    $pad_lvl = 999999;
    include PAD_HOME . 'inits/level.php';
  
    $GLOBALS ['pad_data']    [$pad_lvl] = pad_make_data ( $fake );
    $GLOBALS ['pad_current'] [$pad_lvl] = reset ( $GLOBALS ['pad_data'] );
    $GLOBALS ['pad_key']     [$pad_key] = key ( $GLOBALS ['pad_data'] [$pad_lvl] );
    $GLOBALS ['pad_occur']   [$pad_key] = 1;

    $pad_lvl = $pad_lvl_save;

    return $pad_lvl;

  }


?>