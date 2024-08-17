<?php

  if ( $padOccur [$pad] == 1 ) {
    $padDefault [$pad] = FALSE;
    if ( ! $padEndBase [$pad] and ! $padStartData [$pad] )
      if ( $padWalk [$pad] == 'start' )
        if ( padDefaultData () )
          $padDefault [$pad] = TRUE;
  }
 
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/trace/occur/start.php';  
  if ( $GLOBALS ['padInfoXml']   ) include '/pad/info/xml/occur/start.php';  
  if ( $GLOBALS ['padInfoXref']  ) include '/pad/info/xref/occur/start.php';  

  if ( $GLOBALS ['padInfoXref'] ) 
    padInfoXref ( 'occur', 'start', $padInfoOccur );
  
?>