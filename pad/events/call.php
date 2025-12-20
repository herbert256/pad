<?php

  global $padInfoTrace, $padInfoTraceCall;

  if ( $padInfoTrace )
    if ( $padInfoTraceCall )
      padInfoTrace ( 'call', 'info', $padCall );

?>