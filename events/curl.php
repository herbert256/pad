<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $GLOBALS ['padInfoTraceCurl'] )
    return;

  padInfoTrace ( 'curl', 'file', $url ); 

?>