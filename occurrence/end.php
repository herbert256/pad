<?php

  if ( $padLog )
    include pad . 'log/occurEnd.php';

  if ( $padTrace )
    include 'trace/end.php';
  
  $padInOccur = FALSE;
  
  $padResult [$pad] .= $padHtml [$pad];
  
  padReset ($pad);

?>