<?php

  if     ( $padNull [$pad] ) $padBase [$pad] = '';
  elseif ( $padElse [$pad] ) $padBase [$pad] = $padFalse [$pad]; 

  if ( padXref ) 
    include pad . 'tail/types/xref/items/status.php';  

?>