<?php

  padTrace ( 'occur', 'end', $padPad [$pad] );

  if ( ! $padTraceOccur )
    return;
  
  if ( $padTraceOccurHasDir [$pad] [$padOccur[$pad]] )
    padTraceCheckLocal ( $padTraceOccurDirName [$pad] [$padOccur[$pad]] );

  if ( $padTraceResult and $padPad [$pad] and strlen ( $padPad [$pad] ) > 50 )
    padTraceFile ( 'occ-result', 'pad', $padPad [$pad] ); 
   
?>