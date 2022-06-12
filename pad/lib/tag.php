<?php
  

    function pad_field_tag_nr ($tag, $nr) {

    $lvl = pad_field_tag_lvl ($tag, FALSE);
    $idx = intval ($nr) - 1 ;

    global $pad_parameters;
    
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

    if ( in_array ( $field, ['fields','names','values'] ) )
      $pad_idx = pad_field_tag_lvl ($tag, TRUE);
    else
      $pad_idx = pad_field_tag_lvl ($tag, FALSE);

#    if ( ! isset( $GLOBALS['pad_key'] [$pad_idx] ) or ! isset( $GLOBALS['data'] [$pad_idx] ))
#      $pad_idx--;
    
    if ( pad_file_exists ( PAD . "tag/".$field.".php" ) )
      return include PAD . "tag/$field.php";

    if ( in_array ( $parm, ['name','value'] ) and $pad_idx and isset($GLOBALS['pad_current'] ) ) {
      $pos = 1;
      foreach( $GLOBALS['pad_current'] [$pad_idx] as $key => $value )
        if ( $pos++ == $field )
          return ( $parm == 'name') ? $key : $value;
    }

    return PAD_NOT_FOUND;

  }


  function pad_field_tag_lvl  ($search, $data) {

    $return = pad_field_tag_lvl_base ($search, $data);
    if ( ! $return === FALSE)
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


  function pad_field_tag_lvl_fake ($fake, $name) {

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