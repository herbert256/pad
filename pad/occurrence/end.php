<?php
    
  $padResult [$pad] .= $padOut [$pad];
    
  if ( $GLOBALS ['padInfo'] )
    include 'events/occurEnd.php';  
     
  padResetOcc ($pad);

?>