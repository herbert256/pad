<?php

  padTrace ( 'occur', 'end', $padPad [$pad] );

  if ( ! $padTraceItems ['occur'] )
    return;
  
  if ( $padTraceOccurHasDir [$pad] [$padOccur[$pad]] and $padTraceOccurDir [$pad] [$padOccur[$pad]] )
    padTraceCheckLocal ( $padTraceOccurDirName [$pad] [$padOccur[$pad]] );

  if ( $padTraceItems ['result'] and $padPad [$pad] and strlen ( $padPad [$pad] ) > 50 )
    padTraceFile ( 'occ-result', 'pad', $padPad [$pad] ); 
   
?>