<?php

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'end', 'value=' . $padVal );

?>