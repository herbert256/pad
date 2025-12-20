<?php

  global $padInfoTrace, $padInfoTraceGet;

  if ( function_exists ('padInfoTrace') )
    if ( $padInfoTrace )
      if ( $padInfoTraceGet )
         padInfoTrace ( 'file', 'get', $file );

?>