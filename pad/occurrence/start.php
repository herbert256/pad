<?php

  $padOccur [$pad]++;
 
  $padParm = $padOpt [$pad] [1] ?? '';

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padOut     [$pad] = $padBase [$pad];
  $padKey     [$pad] = key($padData [$pad]);
  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];
 
  if ( $GLOBALS ['padInfo'] )
    include 'events/occurStart.php';

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include 'occurrence/table.php';
  include 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include 'callback/row.php' ;

?>