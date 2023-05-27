<?php

  $GLOBALS ["padSeq_mf_$padSeqSeq"] = [];
 
  foreach ( $padPrm [$pad] as $padSeqOptName => $padSeqOptValue )

    if ( padExists ( pad . "sequence/types/$padSeqOptName/$padSeqFilterCheck.php" ) ) {

      $GLOBALS ["padSeq_mf_$padSeqSeq"] [$padSeqOptName] = $padSeqOptValue;

      padDone ( $padSeqOptName, TRUE );

      $GLOBALS ["padSeq_$padSeqOptName"] = $padSeqOptValue;

    }
   
  $padSeqStoreGet = $padSeqParm;
  $padSeqFor = $padSeqStore [$padSeqParm];

  if ( ! $padSeqPush and ! $padPair )
    $padSeqPush = $padSeqParm;

  include "sequence/type/for.php";
      
?>