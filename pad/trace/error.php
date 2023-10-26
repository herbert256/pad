<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['error'] )
    return;

  padTrace ( 'error', 'info', $error );

?>