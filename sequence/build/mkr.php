<?php

  $padSeqMkrGo = [];

  foreach ( $padPrm [$pad] as $padSeqMkrName => $padSeqMkrVal )

    if ( in_array ( $padSeqMkrName, $padSeqMkr ) ) {

      padDone ( $padSeqMkrName );

      if ( $padSeqMkrName == $padSeqSeq ) 
        return;

      $GLOBALS ["padSeq_$padSeqMkrName"] = $padSeqMkrVal;

      $padSeqMkrArr = padExplode ( $padSeqMkrVal, ';');

      foreach ( $padSeqMkrArr as $padSeqMkrOne ) {

        $padSeqMkrPrm = padExplode ( $padSeqMkrOne, '=');

        $padSeqMkrV1 = $padSeqMkrPrm [0];
        $padSeqMkrV2 = $padSeqMkrPrm [1] ?? '';

        padDone ( $padSeqMkrV1 );

        $GLOBALS ["padSeq_$padSeqMkrV1"] = $padSeqMkrV2;

        $padSeqMkrGo [$padSeqMkrName] [$padSeqMkrV1] = $padSeqMkrV2;

      }

    }

?>