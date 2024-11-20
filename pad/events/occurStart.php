<?php

  if ( $GLOBALS ['padInfoTrace'] ) include 'info/types/trace/occur/start.php';  
  if ( $GLOBALS ['padInfoXml']   ) include 'info/types/xml/occur/start.php';  
  if ( $GLOBALS ['padInfoXref']  ) include 'info/types/xref/occur/start.php';  
  

  if ( $GLOBALS ['padInfoXref']  ) 
    padInfoXref ( 'occurrence', 'start', $padInfoOccur );


?>