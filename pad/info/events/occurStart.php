<?php

  if ( $padOccur [$pad] == 1 ) {
    $padDefault [$pad] = FALSE;
    if ( ! $padEndBase [$pad] and ! $padStartData [$pad] )
      if ( $padWalk [$pad] == 'start' )
        if ( padDefaultData () )
          $padDefault [$pad] = TRUE;
  }
 
  if ( padTrace ) include pad . 'info/types/trace/occur/start.php';  
  if ( padXml   ) include pad . 'info/types/xml/occur/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/occur/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/events/occur.php';   
  
?>