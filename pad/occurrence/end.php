<?php
    
  $padResult [$pad] .= $padPad [$pad];
  
  if ( $padTraceActive and $padBase [$pad] )
    include pad . 'trace/trace/occur/end.php';  
  
  if ( $padTraceTree )
    include pad . 'trace/tree/occur/end.php';  
     
  padResetOcc ($pad);

?>