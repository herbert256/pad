<?php

  include pad . 'trace/items/result.php';  

  padTrace ( 'level', 'end', $padResult[$pad] );

  if ( ( $padTraceTrace or $padTracelocal ) and $padTraceLevelDir )
    padTraceCheckLocal ( $padTraceLevelDirName [$pad] );

  if ( $pad and $padTraceLevelDir ) 
    padTraceStatus ();
  
?>