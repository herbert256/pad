<?php

  if ( $padSeqSeq <> $padSeqMain                      ) return;
  if ( ! count ( $padSeqOperations )                  ) return;
  if ( $padSeqNoMain                                  ) return;
  if ( $padSeqOperations [0] ['padSeqType'] <> 'make' ) return;

  if ( $padSeqMain == 'loop' ) {

    $padSeqLoopCheck = $padSeqOperations [0] ['padSeqSeq'];
    
    if ( ! file_exists ( "/pad/sequence/types/$padSeqLoopCheck/loop.php" ) )
      return;
  
  } else {
  
    $padSeqFixed = include "/pad/sequence/types/$padSeqSeq/fixed.php"; 
  
  }

  $padSeqSeq  = $padSeqOperations [0] ['padSeqSeq'];
  $padSeqParm = $padSeqOperations [0] ['padSeqParm'];

  unset ( $padSeqOperations [0] );

?>