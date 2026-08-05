<?php

  // Fires from padFileGet() in lib/file.php once a path has passed validation and just before
  // it is read, logging it as a 'file get' trace line when $padInfoTraceGet is on.
  //
  // Guarded by function_exists() because files are read during boot, before the trace library
  // itself has been loaded.

  global $padInfoTrace, $padInfoTraceGet;

  if ( function_exists ('padInfoTrace') )
    if ( $padInfoTrace )
      if ( $padInfoTraceGet )
         padInfoTrace ( 'file', 'get', $file );

?>