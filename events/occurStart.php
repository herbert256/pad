<?php

  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/types/trace/occur/start.php';  
  if ( $GLOBALS ['padInfoXml']   ) include '/pad/info/types/xml/occur/start.php';  
  if ( $GLOBALS ['padInfoXref']  ) include '/pad/info/types/xref/occur/start.php';  
  

  if ( $GLOBALS ['padInfoXref']  ) 
    padInfoXref ( 'occurrence', 'start', $padInfoOccur );


?>