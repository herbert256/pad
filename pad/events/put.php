<?php

  // Fires from padFilePut() in lib/file.php after the target path has been checked and before
  // anything is written, logging it as a 'file put' trace line when $padInfoTracePut is on.
  //
  // Guarded by function_exists() for the same reason as events/get.php: writes can happen
  // before the trace library exists.

  global $padInfoTrace, $padInfoTracePut;

  if ( function_exists ('padInfoTrace') )
    if ( $padInfoTrace )
      if ( $padInfoTracePut )
         padInfoTrace ( 'file', 'put', $file );

?>