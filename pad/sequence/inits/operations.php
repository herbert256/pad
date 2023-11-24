<?php

  $padSeqOprGo = $padSeqOprType = [];

  $padSeqOprType [$padSeqSeq] = padSeqMakeType ( "$padSeqTypes/$padSeqSeq" );

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprVal ) {

    if ( $padSeqOprName == $padSeqSeq ) 
      continue;

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) {

      $padSeqOprArr = padExplode ( $padSeqOprVal, ';');

      foreach ( $padSeqOprArr as $padSeqOprOne ) {

        $padSeqOprPrm = padExplode ( $padSeqOprOne, '=');

        $padSeqOprGo   [$padSeqOprName] [$padSeqOprPrm[0]] = $padSeqOprPrm [1] ?? '';
        $padSeqOprType [$padSeqOprPrm[0]] = padSeqMakeType ("$padSeqTypes/$padSeqOprPrm[0]");

        if ( $padSeqOprName == 'make' and $padSeqOprType [$padSeqOprPrm[0]] == 'function' ) 
          include_once "$padSeqTypes/$padSeqOprPrm[0]/function.php";
        elseif ( $padSeqOprName <> 'make' ) 
          include_once "$padSeqTypes/$padSeqOprPrm[0]/bool.php";

      }

    } elseif ( $padSeqOprName <> 'random' and padSeqMakeType ( "$padSeqTypes/$padSeqOprName" ) ) {

      $padSeqOprGo   [$padSeqOprName] = $padSeqOprVal;
      $padSeqOprType [$padSeqOprName] = padSeqMakeType ( "$padSeqTypes/$padSeqOprName" );

      if ( $padSeqOprType [$padSeqOprName] == 'function' ) 
        include_once "$padSeqTypes/$padSeqOprName/function.php";

    }

  }
 
?>