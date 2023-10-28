<?php

  if     ( $padNull [$pad] ) $padBase [$pad] = '';
  elseif ( $padElse [$pad] ) $padBase [$pad] = $padFalse [$pad];    
  elseif ( $padText [$pad] ) $padBase [$pad] = $padTagResult;
  else                       $padBase [$pad] = $padTrue [$pad];

  if ( $padTraceActive )
    include pad . 'trace/files/base.php';  

?>