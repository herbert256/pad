<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'var', 'end', 'value=' . $padVal );
   
?>