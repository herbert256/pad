<?php

  if ( $padSeqSeq == 'pull' )
    if ( count ( $padSeqList) )
      if ( $padSeqList [0] ['padSeqType'] == 'make' ) {
        $padSeqFixed = include '/pad/sequence/types/pull/fixed.php';
        extract ( $padSeqList [0] );
        unset   ( $padSeqList [0] );
        $padSeqSeq   = $padSeqName;
        $padSeqParm  = $padSeqValue;
      }

?>