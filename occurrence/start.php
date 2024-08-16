<?php

  $padOccur [$pad]++;

  $padParm = $padOpt [$pad] [1] ?? '';

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padPad     [$pad] = $padBase [$pad];
  $padKey     [$pad] = key($padData [$pad]);
  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];
 
  if ( $GLOBALS['padInfo'] )
    include '/pad/info/events/occurStart.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include '/pad/occurrence/table.php';
  include '/pad/occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include '/pad/callback/row.php' ;

?>