<?php

  if ( padEvalBool ( $padIf ) ) {

    $padReturn  = $padContent;
    $padContent = '';
    
    return $padReturn;
  
  } else
  
    return FALSE;

?>