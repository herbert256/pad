<?php

  $pad_parameters [$pad_lvl] ['tag_count']++;

  $pad_data [$pad_lvl]     = [];
  $pad_data [$pad_lvl] [1] = [];

  if ( $pad_lvl == 1 )
    $pad_data [1] [1] = &$GLOBALS;

  include PAD_HOME . "level/type.php";

  if (pad_tag_parm ('content') ) 
    $pad_content .= pad_content (pad_tag_parm ('content'));    

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( $pad_tag_result === NULL or ! isset ($pad_tag_result) ) {
    $pad_tag_result = $pad_content = $pad_false = $pad_tag_ob = '';
    $pad_data [$pad_lvl] = [];
  }

  if ($pad_lvl > 1)
    $pad_data [$pad_lvl] = pad_data ( $pad_data [$pad_lvl] );   

  if ( is_array($pad_tag_result) ) pad_add_array_to_data ( $pad_tag_result,       pad_tag_parm('type') );
  if ( pad_tag_parm ('data')     ) pad_add_array_to_data ( pad_tag_parm ('data'), pad_tag_parm('type') );

  if     ( is_array($pad_tag_result) ) $pad_base [$pad_lvl] = ( count($pad_data [$pad_lvl]) ) ? $pad_content : $pad_false;
  elseif ( $pad_tag_result === FALSE ) $pad_base [$pad_lvl] = $pad_false;
  elseif ( $pad_tag_result === TRUE  ) $pad_base [$pad_lvl] = $pad_content;
  else                                 $pad_base [$pad_lvl] = $pad_content . $pad_tag_result;

  $pad_base [$pad_lvl] .= $pad_tag_ob;

  if ( ! count ($pad_data [$pad_lvl] ) ) {
    $pad_base [$pad_lvl] = $pad_false;
    $pad_data [$pad_lvl] [1] = [];
  }

  reset ( $pad_data[$pad_lvl] );

?>