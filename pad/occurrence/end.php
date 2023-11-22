<?php
    
  $padResult [$pad] .= $padPad [$pad];
  
  if ( $padTraceActive and $padBase [$pad] )
    include pad . 'tail/types/trace/occur/end.php';  
  
  if ( $padXml )
    include pad . 'tail/types/xml/occur/end.php';  
     
  padResetOcc ($pad);

?>