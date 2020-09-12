<?php

  define ( 'PAD_NOT_FOUND', '*pad*Floe%pende!Flap*pad*' );

  function pad_field_check ($parm) { return pad_field ($parm, 1); } 
  function pad_field_value ($parm) { return pad_field ($parm, 2); } 
  function pad_array_check ($parm) { return pad_field ($parm, 3); } 
  function pad_array_value ($parm) { return pad_field ($parm, 4); } 

  function pad_field ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = pad_field_tag    ( $field );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = pad_field_prefix ( $field );
    else                                        $value = pad_field_level  ( $field );

    if      ($type == 1) return ( $value !== NULL and ( $value === PAD_NOT_FOUND or ! is_scalar($value) ) ) ? FALSE : TRUE;
    else if ($type == 2) return ( $value === NULL or    $value === PAD_NOT_FOUND or ! is_scalar($value)   ) ? NULL  : $value;
    else if ($type == 3) return ( $value === NULL or    $value === PAD_NOT_FOUND or   is_scalar($value)   ) ? FALSE : TRUE;
    else                 return ( $value === NULL or    $value === PAD_NOT_FOUND or   is_scalar($value)   ) ? NULL  : $value;

  }
  
  function pad_field_tag ($field) {

    $temp  = explode ('#', $field, 3);
    $tag   = $temp[0];
    $field = $temp[1];
    $parm  = $temp[2]??'';

    $pad_idx = pad_idx ($tag);

    if ( file_exists ( PAD_HOME . "tag/".$field.".php" ) )
      return include PAD_HOME . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] )  and $pad_idx) {
      $pos = 1;
      foreach( $GLOBALS['pad_current'] [$pad_idx] as $key => $value )
        if ( $pos++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    return PAD_NOT_FOUND;
    
  }

  function pad_field_prefix ( $field ) {

    global $pad_lvl, $pad_db_lvl, $pad_current;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( $prefix == 'PHP' )
      return pad_field_search ($GLOBALS, $field);

    $lvl = pad_find_lvl ( $prefix );

    if ( $lvl )
      return pad_field_search ($pad_current [$lvl], $field);
    else
      for ( $i=$pad_lvl; $i; $i-- )
        foreach ( $pad_db_lvl [$i] as $key => $value)
          if ($key == $prefix)
            return pad_field_search ($value, $field);

    return PAD_NOT_FOUND;
    
  }


  function pad_field_level ( $field ) {

    global $pad_lvl, $pad_db_lvl, $pad_current;

    for ( $i=$pad_lvl; $i; $i-- ) {

      $work = pad_field_search ( $pad_current[$i], $field );
      if ( $work !== PAD_NOT_FOUND )
        return $work;

      foreach ( $pad_db_lvl [$i] as $key => $value ) {
        $work = pad_field_search ( $value, $field);   
        if ( $work !== PAD_NOT_FOUND )
          return $work;
      }

    }

    return PAD_NOT_FOUND;
    
  }


  function pad_field_search ($current, $field) {

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

    return $current;

  }

?>