<?php

  if ( $GLOBALS ['padInfoTrace'] ) include PAD . 'info/types/trace/occur/start.php';  
  if ( $GLOBALS ['padInfoXml']   ) include PAD . 'info/types/xml/occur/start.php';  
  if ( $GLOBALS ['padInfoXref']  ) include PAD . 'info/types/xref/occur/start.php';  
  

  if ( $GLOBALS ['padInfoXref']  ) 
    padInfoXref ( 'occurrence', 'start', $padInfoOccur );


?>