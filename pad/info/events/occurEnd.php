<?php

  if ( $GLOBALS ['padInfo'] and $padBase [$pad] )
    include '/pad/info/types/trace/occur/end.php';  
  
  if ( padXml  ) include '/pad/info/types/xml/occur/end.php';  
  if ( $GLOBALS ['padInfo'] ) include '/pad/info/types/xref/occur/end.php';  
  
?>