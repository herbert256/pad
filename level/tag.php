<?php

  $pad_parameters [$pad_lvl] ['tag_count']++;

 if ( pad_tag_parm ('content') ) 
    $pad_content .= include PAD_HOME . "options/content.php";    

  $pad_tag_result = include PAD_HOME . "level/type.php";

  $pad_parameters [$pad_lvl] ['tag_result'] = pad_info ($pad_tag_result);
  
  if ( $pad_tag_result === NULL )
    return include PAD_HOME . "level/null.php"; 

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( is_array ( $pad_tag_result ) )
    pad_add_array_to_data ($pad_tag_result);

  if ( pad_tag_parm ('data') ) 
    $pad_data [$pad_lvl] = include PAD_HOME . "options/data.php"; 

  if ( pad_tag_parm ('sequence') ) 
    $pad_data [$pad_lvl] = include PAD_HOME . "options/sequence.php"; 

  if ($pad_lvl > 1)
    $pad_data [$pad_lvl] = pad_make_data ( $pad_data [$pad_lvl] );   

  if     ( ! count($pad_data [$pad_lvl]) ) $pad_base [$pad_lvl] = $pad_false;
  elseif ( is_array($pad_tag_result) )     $pad_base [$pad_lvl] = $pad_content;
  elseif ( $pad_tag_result === FALSE )     $pad_base [$pad_lvl] = $pad_false;
  elseif ( $pad_tag_result === TRUE  )     $pad_base [$pad_lvl] = $pad_content;
  else                                     $pad_base [$pad_lvl] = $pad_tag_result . $pad_content;

  if ( $pad_false and $pad_base [$pad_lvl] == $pad_false and ! count($pad_data [$pad_lvl]) )
    $pad_data [$pad_lvl] [0] = [];

  $pad_base [$pad_lvl] .= $pad_tag_content;

  return $pad_tag_result;
  
?>