<?php

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'start', 'var=' . $padFld . ' options=' . padJson($padOpts) );

?>
