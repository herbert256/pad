<?php

  if ( isset ( $GLOBALS ['padCode'] ) )
    unset ( $GLOBALS ['padCode'] );

  $GLOBALS ['padFunction'] = TRUE;

  return include pad . 'start/pad.php'; 

?>