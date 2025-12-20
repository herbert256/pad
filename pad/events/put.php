<?php

  global $padInfoTrace, $padInfoTracePut;

  if ( function_exists ('padInfoTrace') )
    if ( $padInfoTrace )
      if ( $padInfoTracePut )
         padInfoTrace ( 'file', 'put', $file );

?>
