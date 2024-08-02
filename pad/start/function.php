<?php

  if ( isset ( $GLOBALS ['padCode'] ) )
    unset ( $GLOBALS ['padCode'] );

  $GLOBALS ['padFunction'] = FALSE;
  $GLOBALS ['padGlobals']  = TRUE;

  return include pad . 'start/pad.php'; 

?>