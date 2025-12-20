<?php

  if ( function_exists ('padInfoTrace') )
    if ( $GLOBALS ['padInfoTrace'] )
      if ( $GLOBALS ['padInfoTracePut'] )
         padInfoTrace ( 'file', 'put', $file );

?>
