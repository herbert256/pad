<?php

  $padOccur [$pad]++;

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padPad       [$pad] = $padBase [$pad];
  $padKey       [$pad] = key($padData [$pad]);
  $padCurrent   [$pad] = $padData [$pad] [$padKey [$pad]];
  $padOccurType [$pad] = $padOccurTypeSet;

  if ( $padTraceActive )
    include pad . 'trace/trace/occur/start.php';  

  if ( $padTraceTree )
    include pad . 'trace/tree/occur/start.php';  
  
  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include pad . 'occurrence/table.php';
  include pad . 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include pad . 'callback/row.php' ;

?>