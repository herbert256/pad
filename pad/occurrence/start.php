<?php

  $padOccur [$pad]++;
  $padHtml  [$pad] = $padBase [$pad];
  $padKey   [$pad] = key($padData [$pad]);

  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  $padInOccur = TRUE;
  
  include 'trace/start.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include 'set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include PAD . 'callback/row.php' ;

?>