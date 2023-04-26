<?php
  
  if ( padTagParm ('null') ) {
    $padOptFlag = include pad . "options/null.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = NULL;
  }

  if ( padTagParm ('false') ) {
    $padOptFlag = include pad . "options/false.php"; 
    if ( ! $padOptFlag ) 
      $padTagResult = FALSE;
  }
  
?>