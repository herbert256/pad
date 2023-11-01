<?php

  if ( ! $padTraceItems ['sequence'] )
    return;

  padTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 

  padTraceFile ( 'sequence', 'json', $padSeqReturn );

?>