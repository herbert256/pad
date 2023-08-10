<?php

  $padSeqOprGo = [];

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprVal ) {

    if ( $padSeqOprName == $padSeqSeq ) 
      return;

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) {

      $padSeqOprArr = padExplode ( $padSeqOprVal, ';');

      foreach ( $padSeqOprArr as $padSeqOprOne ) {

        $padSeqOprPrm = padExplode ( $padSeqOprOne, '=');

        $padSeqOprGo [$padSeqOprName] [$padSeqOprPrm[0]] = $padSeqOprPrm [1] ?? '';

      }

    } elseif ( padExists ( "$padSeqTypes/$padSeqOprName/make.php" ) )

      $padSeqOprGo [$padSeqOprName] = $padSeqOprVal;

  }
    
?>