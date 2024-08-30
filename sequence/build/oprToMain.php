<?php

  if ( ! count ( $padSeqOperations )                  ) return;
  if ( $padSeqOperations [0] ['padSeqType'] <> 'make' ) return;
  if ( isset ( $padPrm [$pad] ['build'] )             ) return;
  if ( isset ( $padPrm [$pad] ['noMain'] )            ) return;

  $padSeqSeq   = $padSeqOperations [0] ['padSeqSeq'];
  $padSeqParm  = $padSeqOperations [0] ['padSeqParm'];
  $padSeqBuild = $padSeqOperations [0] ['padSeqBuild'];

  unset ( $padSeqOperations [0] );

?>