<?php
    
  $padResult [$pad] .= $padPad [$pad];
  
  if ( $padTraceActive and $padBase [$pad] )
    include pad . 'trace/occur/end.php';  
  
  if ( $padXml )
    include pad . 'xml/occur/end.php';  
     
  padResetOcc ($pad);

?>