<?php

  $padSeqOprGo = $padSeqOprType = [];

  $padSeqOprType [$padSeqSeq] = padSeqMakeType ( "/pad/seq/types/$padSeqSeq" );

  foreach ( $padPrm [$pad] as $padSeqOprName => $padSeqOprVal ) {

    if ( $padSeqOprName == $padSeqSeq ) 
      continue;

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) {

      $padSeqOprArr = padExplode ( $padSeqOprVal, ';');

      foreach ( $padSeqOprArr as $padSeqOprOne ) {

        $padSeqOprPrm = padExplode ( $padSeqOprOne, '=');

        $padSeqOprGo   [$padSeqOprName] [$padSeqOprPrm[0]] = $padSeqOprPrm [1] ?? '';
        $padSeqOprType [$padSeqOprPrm[0]] = padSeqMakeType ("/pad/seq/types/$padSeqOprPrm[0]");

        if ( $padSeqOprName == 'make' and $padSeqOprType [$padSeqOprPrm[0]] == 'function' ) 
          include_once "/pad/seq/types/$padSeqOprPrm[0]/function.php";
        elseif ( $padSeqOprName <> 'make' ) 
          include_once "/pad/seq/types/$padSeqOprPrm[0]/bool.php";

      }

    } elseif ( $padSeqOprName <> 'random' and padSeqMakeType ( "/pad/seq/types/$padSeqOprName" ) ) {

      $padSeqOprGo   [$padSeqOprName] = $padSeqOprVal;
      $padSeqOprType [$padSeqOprName] = padSeqMakeType ( "/pad/seq/types/$padSeqOprName" );

      if ( $padSeqOprType [$padSeqOprName] == 'function' ) 
        include_once "/pad/seq/types/$padSeqOprName/function.php";

    }

  }
 
?>