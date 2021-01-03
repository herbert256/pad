<?php

  $pad_parameters [$pad_lvl] ['tag_count']++;

  $pad_data [$pad_lvl] = [];
  if ( $pad_lvl == 1 )
    $pad_data [$pad_lvl] [1] = &$GLOBALS;
  else
    $pad_data [$pad_lvl] [1] = [];

  include PAD_HOME . "level/type.php";

  if ( $pad_tag_result === NULL )
    return include PAD_HOME . "level/null.php"; 

  if ( pad_tag_parm ('content') ) include PAD_HOME . "parms/content.php";    
  if ( pad_tag_parm ('data')    ) include PAD_HOME . "parms/data.php"; 

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result      = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result      = pad_xxx_to_array ( $pad_tag_result );
  if ( is_array    ( $pad_tag_result ) ) $pad_data [$pad_lvl] = $pad_tag_result;

  if ($pad_lvl > 1)
    $pad_data [$pad_lvl] = pad_data ( $pad_data [$pad_lvl] );   

  if     ( ! count($pad_data [$pad_lvl]) ) $pad_base [$pad_lvl] = $pad_false;
  elseif ( is_array($pad_tag_result) )     $pad_base [$pad_lvl] = $pad_content;
  elseif ( $pad_tag_result === FALSE )     $pad_base [$pad_lvl] = $pad_false;
  elseif ( $pad_tag_result === TRUE  )     $pad_base [$pad_lvl] = $pad_content;
  else                                     $pad_base [$pad_lvl] = $pad_tag_result;

  $pad_base [$pad_lvl] .= $pad_tag_ob;

  pad_trace ("tag/base", $pad_base [$pad_lvl], TRUE);

  reset ( $pad_data[$pad_lvl] );
  
?>