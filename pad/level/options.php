<?php

  if ( pTag_parm ('content') ) $pTrue [$pad] = include PAD . "options/content.php";    
  if ( pTag_parm ('else')    ) $pFalse   [$pad] = include PAD . "options/else.php";    

  if ( pTag_parm ('null') ) {
    $pOpt_flag = include PAD . "options/null.php"; 
    if ( ! $pOpt_flag ) 
      $pTag_result = NULL;
  }

  if ( pTag_parm ('false') ) {
    $pOpt_flag = include PAD . "options/false.php"; 
    if ( ! $pOpt_flag ) 
      $pTag_result = FALSE;
  }

  if ( pTag_parm ('data') )
    if ( is_array ( $pTag_result ) )
      $pTag_result = include PAD . "options/data.php";   
    else
      $pData [$pad] = include PAD . "options/data.php";   
  
?>