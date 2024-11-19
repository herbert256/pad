<?php
    
  $padResult [$pad] .= $padOut [$pad];
    
  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/occurEnd.php';  
     
  padResetOcc ($pad);

?>