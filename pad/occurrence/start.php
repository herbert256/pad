<?php

  $padOccur [$pad]++;

  $padPad       [$pad] = $padBase [$pad];
  $padKey       [$pad] = key($padData [$pad]);
  $padCurrent   [$pad] = $padData [$pad] [$padKey [$pad]];
  $padOccurType [$pad] = $padOccurTypeSet;;

  $padOccurTypeSet  = 'default';

  if ( $padTraceActive )
    include pad . 'trace/occur/start.php';  
  
  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include pad . 'occurrence/table.php';
  include pad . 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include pad . 'callback/row.php' ;

?>