<?php

  if ( $padLog )
    include PAD . 'log/occurEnd.php';

  if ( $padTrace )
    include 'trace/end.php';
  
  $padInOccur = FALSE;
  
  $padResult [$pad] .= $padHtml [$pad];
  
  if ( $pad )
    padReset ($pad);

?>