<?php

  $GLOBALS ["padSeq_mf_$padSeqSeq"] = [];
 
  foreach ( $padPrmsTag [$pad] as $padSeqOptName => $padSeqOptValue )

    if ( file_exists ( PAD . "pad/sequence/types/$padSeqOptName/$padSeqFilterCheck.php" ) ) {

      $GLOBALS ["padSeq_mf_$padSeqSeq"] [$padSeqOptName] = $padSeqOptValue;

      padDone ( $padSeqOptName, TRUE );

      $GLOBALS ["padSeq_$padSeqOptName"] = $padSeqOptValue;

    }
   
  $padSeqStoreGet = $padSeqParm;
  $padSeqFor = $padSeqStore [$padSeqParm];

  if ( ! $padSeqPush and ! $padPair )
    $padSeqPush = $padSeqParm;

  include PAD . "pad/sequence/type/for.php";
      
?>