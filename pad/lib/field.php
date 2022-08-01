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
      return pad_field_search ($GLOBALS, $field, $type);
    elseif ( $lvl ) 
      return pad_field_search ($pad_current [$lvl], $field, $type);
    else
      for ( $i=$pad_lvl; $i; $i-- )
        foreach ( $pad_db_lvl [$i] as $key => $value)
          if ($key == $prefix)
            return pad_field_search ($value, $field, $type);

    return INF;
    
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


?>