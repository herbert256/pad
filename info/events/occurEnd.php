<?php

  if ( $GLOBALS ['padInfoTrace'] and $padBase [$pad] )
    include '/pad/info/trace/occur/end.php';  
  
  if ( $GLOBALS ['padInfoXml']  ) include '/pad/info/xml/occur/end.php';  
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/xref/occur/end.php';  
  
?>