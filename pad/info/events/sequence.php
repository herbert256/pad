<?php

  if ( $padInfTrace and $padInfTraceSequence ) {

   padTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 
   padTrace ( 'sequence', $padSeqSeq, $padSeqReturn ); 

  }

  
  padTrace ( 'sequences', 'types', $padSeqSeq );

  if ( $padSeqFor !== FALSE )
   padTrace ( 'sequences', 'builds', 'for' );
  else
   padTrace ( 'sequences', 'builds', $padSeqBuild );


  padXapp ( 'sequences', 'types', $padSeqSeq );

  if ( $padSeqFor !== FALSE )
    padXapp ( 'sequences', 'builds', 'for' );
  else
    padXapp ( 'sequences', 'builds', $padSeqBuild );

?>