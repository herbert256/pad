<?php

  if     ( $padNull [$pad] ) $padBase [$pad] = '';
  elseif ( $padElse [$pad] ) $padBase [$pad] = $padFalse; 
  else                       $padBase [$pad] = $padContent;

  if ( padXref ) 
    include pad . 'info/types/xref/items/status.php';  

?>