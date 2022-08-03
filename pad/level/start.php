<?php

  $pad_lvl++;
  
  include 'inits.php';

  include PAD . "level/type.php";

  if ( pad_tag_parm ('content') ) $pad_content  = include PAD . "options/content.php";    
  if ( pad_tag_parm ('else')    ) $pad_false    = include PAD . "options/else.php";    
  if ( pad_tag_parm ('flag')    ) $pad_opt_flag = include PAD . "options/flag.php";    

  if ( pad_tag_parm ('flag') and ! $pad_opt_flag ) 
    $pad_tag_result = FALSE;

  if ( pad_tag_parm ('data') ) {
    if ( is_array ( $pad_tag_result ) )
      $pad_tag_result = include PAD . "options/data.php";   
    else
      $pad_data [$pad_lvl] = include PAD . "options/data.php";   

  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pad_parameters [$pad_lvl] ['content'] = $pad_content;
  $pad_parameters [$pad_lvl] ['false']   = $pad_false;
  $pad_parameters [$pad_lvl] ['true']    = $pad_true;
  $pad_parameters [$pad_lvl] ['null']    = $pad_null;
  $pad_parameters [$pad_lvl] ['else']    = $pad_else;
  $pad_parameters [$pad_lvl] ['array']   = $pad_array;
  $pad_parameters [$pad_lvl] ['text']    = $pad_text;
  $pad_parameters [$pad_lvl] ['default'] = pad_is_default_data ( $pad_data [$pad_lvl] );
  
  include 'trace/start.php';

  $pad_options = 'level_start';
  include PAD . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/start.php';
  
?>