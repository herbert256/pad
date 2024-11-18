<?php
    
  $padResult [$pad] .= $padPad [$pad];
    
  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/occurEnd.php';  
     
  padResetOcc ($pad);

?>