<?php

  foreach ( $padSeqOperations as $padSeqOperation ) {

    extract ( $padSeqOperation );

    $GLOBALS [ padSeqName ($padSeqName) ] = $padSeqParm;

    $padSeqLoop = $padSeq;
    $padSeqSave = $padSeq;
    $padSeq     = include '/pad/sequence/build/call.php';

    if     ( $padSeqType == 'keep'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;
    elseif ( $padSeqType == 'keep'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqType == 'keep'   and $padSeq <> $padSeqLoop ) return FALSE;
    elseif ( $padSeqType == 'keep'                              ) $padSeq = $padSeqSave;
    elseif ( $padSeqType == 'remove' and $padSeq === TRUE       ) return FALSE;
    elseif ( $padSeqType == 'remove' and $padSeq === FALSE      ) $padSeq = $padSeqSave;
    elseif ( $padSeqType == 'remove' and $padSeq == $padSeqLoop ) return FALSE;
    elseif ( $padSeqType == 'remove'                            ) $padSeq = $padSeqSave;
    elseif ( $padSeqType == 'make'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqType == 'make'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;

  }

?>