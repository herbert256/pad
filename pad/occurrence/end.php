<?php
    
  $padResult [$pad] .= $padPad [$pad];
  
  if ( $padTraceActive and $padBase [$pad] )
    include pad . 'trace/occur/end.php';  
  
  if ( $padXmlBuild )
    include pad . 'xml/occur/end.php';  
     
  padResetOcc ($pad);

?>