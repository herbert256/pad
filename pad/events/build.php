<?php

  if ( $GLOBALS ['padInfoTrace'] ) 
    if ($padInfoTraceBuild )
      padInfoTrace ( 'build', 'info', $padBase [$pad] ); 
    
  if ( $GLOBALS ['padInfoTrace'] ) include 'info/types/trace/level/start.php';
  if ( $GLOBALS ['padInfoTrace'] ) include 'info/types/trace/level/info.php';    

?>