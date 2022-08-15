<?php

  if ( pTag_parm ('null') ) {
    $padOpt_flag = include PAD . "options/null.php"; 
    if ( ! $padOpt_flag ) 
      $padTagResult = NULL;
  }

  if ( pTag_parm ('false') ) {
    $padOpt_flag = include PAD . "options/false.php"; 
    if ( ! $padOpt_flag ) 
      $padTagResult = FALSE;
  }
  
?>