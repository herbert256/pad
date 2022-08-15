<?php

  if ( padTagParm ('null') ) {
    $padOpt_flag = include PAD . "options/null.php"; 
    if ( ! $padOpt_flag ) 
      $padTagResult = NULL;
  }

  if ( padTagParm ('false') ) {
    $padOpt_flag = include PAD . "options/false.php"; 
    if ( ! $padOpt_flag ) 
      $padTagResult = FALSE;
  }
  
?>