<?php

  if ( ! count ( $padSeqOperations )                  ) return;
  if ( $padSeqOperations [0] ['padSeqType'] <> 'make' ) return;

  $padSeqSeq   = $padSeqOperations [0] ['padSeqSeq'];
  $padSeqParm  = $padSeqOperations [0] ['padSeqParm'];
  $padSeqBuild = $padSeqOperations [0] ['padSeqBuild'];

  if ( file_exists ( "/pas/sequence/types/$padSeqSeq/loop.php" ) )
    $padSeqBuild = 'loop';

  unset ( $padSeqOperations [0] );

?>