<?php

  if ( pad_tag_parm ('content') ) $pad_true [$pad] = include PAD . "options/content.php";    
  if ( pad_tag_parm ('else')    ) $pad_false   [$pad] = include PAD . "options/else.php";    

  if ( pad_tag_parm ('null') ) {
    $pad_opt_flag = include PAD . "options/null.php"; 
    if ( ! $pad_opt_flag ) 
      $pad_tag_result = NULL;
  }

  if ( pad_tag_parm ('false') ) {
    $pad_opt_flag = include PAD . "options/false.php"; 
    if ( ! $pad_opt_flag ) 
      $pad_tag_result = FALSE;
  }

  if ( pad_tag_parm ('data') )
    if ( is_array ( $pad_tag_result ) )
      $pad_tag_result = include PAD . "options/data.php";   
    else
      $pad_data [$pad] = include PAD . "options/data.php";   
  
?>