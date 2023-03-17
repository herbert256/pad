<?php

  $padOccur [$pad]++;
  $padHtml  [$pad] = $padBase [$pad];
  $padKey   [$pad] = key($padData [$pad]);

  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  $padInOccur = TRUE;
  
  if ( $padLog )
    include PAD . 'log/occurStart.php';

  if ( $padTrace )
    include 'trace/start.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include 'set.php';
  include 'table.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include PAD . 'callback/row.php' ;

?>