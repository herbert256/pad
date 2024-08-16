<?php
    
  $padResult [$pad] .= $padPad [$pad];
    
  if ( $GLOBALS['padInfo'] )
    include '/pad/info/events/occurEnd.php';  
     
  padResetOcc ($pad);

?>