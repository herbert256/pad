<?php

  $pad_tag_result = include PAD . "level/type.php";

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( pad_tag_parm ('content') ) $pad_content  = include PAD . "options/content.php";    
  if ( pad_tag_parm ('else')    ) $pad_false    = include PAD . "options/else.php";    
  if ( pad_tag_parm ('flag')    ) $pad_opt_flag = include PAD . "options/flag.php";    

  if ( pad_tag_parm ('flag') and ! $pad_opt_flag ) 
    $pad_tag_result = FALSE;

  $pad_parameters [$pad_lvl] ['true']            = pad_true_false ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['tag_result']      = pad_info ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['tag_result_data'] = $pad_tag_result;

  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pad_data [$pad_lvl] = pad_make_data ( $pad_data [$pad_lvl] );   

  $pad_parameters [$pad_lvl] ['false']      = $pad_false;
  $pad_parameters [$pad_lvl] ['true']       = $pad_true;
  $pad_parameters [$pad_lvl] ['null']       = $pad_null;
  $pad_parameters [$pad_lvl] ['else']       = $pad_else;
  $pad_parameters [$pad_lvl] ['tag_result'] = pad_info ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['default']    = pad_is_default_data ( $pad_data [$pad_lvl] );
  
  if     ( $pad_null ) return NULL;
  elseif ( $pad_else ) return FALSE
  else                 return TRUE;

?>