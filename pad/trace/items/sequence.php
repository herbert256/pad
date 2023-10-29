<?php

  if ( ! $padTraceTypes ['sequence'] )
    return;

  padTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 

  padTraceFile ( 'sequence', 'json', $padSeqReturn );

?>