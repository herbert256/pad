<?php

  if ( $GLOBALS ['padInfoTrace'] ) 
    if ($padInfoTraceBuild )
      padInfoTrace ( 'build', 'info', $padBase [$pad] ); 
    
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/trace/level/start.php';
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/trace/level/info.php';    

?>