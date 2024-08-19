<?php

  if ( $padSeqSeq == 'pull' )
    if ( count ( $padSeqOprList) )
      if ( $padSeqOprList [0] ['padSeqOprType'] == 'make' ) {
        $padSeqFixed = include '/pad/sequence/types/pull/fixed.php';
        extract ( $padSeqOprList [0] );
        unset   ( $padSeqOprList [0] );
        $padSeqSeq   = $padSeqOprName;
        $padSeqParm  = $padSeqOprValue;
      }

?>