<?php

  if     ( $padNull [$pad] ) $padBase [$pad] = '';
  elseif ( $padElse [$pad] ) $padBase [$pad] = $padFalse [$pad];    
  elseif ( $padText [$pad] ) $padBase [$pad] = $padTagResult;
  else                       $padBase [$pad] = $padTrue [$pad];

  if ( $padXref ) 
    include pad . 'tail/types/xref/items/status.php';  

?>