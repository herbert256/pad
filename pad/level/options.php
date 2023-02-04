<?php

  if ( padTagParm ('null') ) {
    $padOptFlag = include PAD . "options/null.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = NULL;
  }

  if ( padTagParm ('false') ) {
    $padOptFlag = include PAD . "options/false.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = FALSE;
  }
  
?>