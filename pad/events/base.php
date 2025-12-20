<?php

  global $padInfoTrace, $padInfoTraceLevelBase;

  if ( $padInfoTrace and $padInfoTraceLevelBase )
    if ( ! $padInfoTraceDouble and $padInfoTraceContent )
      padInfoTrace ( 'level', 'base',  $padBase [$pad] );

?>
