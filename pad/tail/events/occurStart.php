<?php

  if ( $padOccur [$pad] == 1 ) {
    $padDefault [$pad] = FALSE;
    if ( ! $padEndBase [$pad] and ! $padStartData [$pad] )
      if ( $padWalk [$pad] == 'start' )
        if ( padDefaultData () )
          $padDefault [$pad] = TRUE;
  }
 
  if ( padTrace ) include pad . 'tail/types/trace/occur/start.php';  
  if ( padXml   ) include pad . 'tail/types/xml/occur/start.php';  
  if ( padXref  ) include pad . 'tail/types/xref/items/occur.php';  
  
?>