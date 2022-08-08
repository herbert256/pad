<?php

  if ( pTag_parm ('content') ) $pTrue [$p] = include PAD . "options/content.php";    
  if ( pTag_parm ('else')    ) $pFalse   [$p] = include PAD . "options/else.php";    

  if ( pTag_parm ('null') ) {
    $pOpt_flag = include PAD . "options/null.php"; 
    if ( ! $pOpt_flag ) 
      $pTagResult [$p] = NULL;
  }

  if ( pTag_parm ('false') ) {
    $pOpt_flag = include PAD . "options/false.php"; 
    if ( ! $pOpt_flag ) 
      $pTagResult [$p] = FALSE;
  }

  if ( pTag_parm ('data') )
    if ( is_array ( $pTagResult [$p] ) )
      $pTagResult [$p] = include PAD . "options/data.php";   
    else
      $pData [$p] = include PAD . "options/data.php";   
  
?>