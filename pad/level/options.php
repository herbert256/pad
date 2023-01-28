<?php

  if ( padTagParm ('null') ) {
    $padOptFlag = include PAD . "pad/options/null.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = NULL;
  }

  if ( padTagParm ('false') ) {
    $padOptFlag = include PAD . "pad/options/false.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = FALSE;
  }
  
?>