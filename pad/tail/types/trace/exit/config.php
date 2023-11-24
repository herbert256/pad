<?php

  if ( $GLOBALS ['padTraceSession'] )
    foreach ( padSessionEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $GLOBALS ['padTraceStartEnd'] )
     padTrace ( 'trace', 'end' );

  include pad . 'tail/types/trace/trace/end.php';
  
  return TRUE;

?>