<?php

  $padOccur [$pad]++;

  if ( $padOccur [$pad] == 1 ) {
    $padDefault [$pad] = FALSE;
    if ( ! $padAfterBase [$pad] and ! $padBeforeData [$pad] )
      if ( ! $padEndBase [$pad] and ! $padStartData [$pad] )
        if ( $padWalk [$pad] == 'start' )
          if ( padDefaultData () )
            $padDefault [$pad] = TRUE;
  }

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padPad       [$pad] = $padBase [$pad];
  $padKey       [$pad] = key($padData [$pad]);
  $padCurrent   [$pad] = $padData [$pad] [$padKey [$pad]];
  $padOccurType [$pad] = $padOccurTypeSet;

  if ( $padTraceActive ) include pad . 'tail/types/trace/occur/start.php';  
  if ( padXml    ) include pad . 'tail/types/xml/occur/start.php';  
  if ( padXref        ) include pad . 'tail/types/xref/items/occur.php';  
  
  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

  include pad . 'occurrence/table.php';
  include pad . 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include pad . 'callback/row.php' ;

?>