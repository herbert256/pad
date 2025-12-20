<?php

  global $padInfoTrace;

  if ( $padInfoTrace )
    if ($padInfoTraceBuild )
      padInfoTrace ( 'build', 'info', $padBase [$pad] );

  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/start.php';
  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/info.php';

?>
