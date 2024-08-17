<?php
    
  $padResult [$pad] .= $padPad [$pad];
    
  if ( $GLOBALS['padInfo'] )
    include '/pad/events/occurEnd.php';  
     
  padResetOcc ($pad);

?>