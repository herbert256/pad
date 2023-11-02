<?php

  if ( ! $padTraceSequence )
    return;

  padTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 

  padTraceFile ( 'sequence', 'json', $padSeqReturn );

?>