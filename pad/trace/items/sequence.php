<?php

  if ( ! $padTraceTypes ['sequence'] )
    return;

  padTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 

  if ( $padTraceTree )
    padTraceFile ( 'sequence', 'json', $padSeqReturn );

?>