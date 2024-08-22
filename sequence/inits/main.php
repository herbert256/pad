<?php

  if ( $padSeqSeq <> $padSeqMain                      ) return;
  if ( ! count ( $padSeqOperations )                  ) return;
  if ( isset ( $padSeqMake )                          ) return;
  if ( isset ( $padSeqKeep )                          ) return;
  if ( isset ( $padSeqRemove )                        ) return;
  if ( $padSeqNoMain                                  ) return;
  if ( $padSeqOperations [0] ['padSeqType'] <> 'make' ) return;

  $padSeqFixed = include "/pad/sequence/types/$padSeqSeq/fixed.php";

  $padSeqSeq  = $padSeqOperations [0] ['padSeqSeq'];
  $padSeqParm = $padSeqOperations [0] ['padSeqParm'];

  unset ( $padSeqOperations [0] );

?>