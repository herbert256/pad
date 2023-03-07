<?php

  $GLOBALS ["padSeq_$padSeqOne" . "_list"] = [];

  if ( $padSeqOne == $padSeqSeq ) 
    return;

  if ( ! isset($padPrm [$pad] [$padSeqOne]) )
    return;

  $GLOBALS["padSeq_".$padSeqOne. "_list"] = [];

  $padSeqOneTmp = padExplode ( $padPrm [$pad] [$padSeqOne], ';');

  $padSeqOneList = [];
  foreach ( $padSeqOneTmp as $padEntry )
    $padSeqOneList [$padEntry ] = TRUE;

  foreach ( $padSeqOneList as $padSeqOneName => $padSeqOneValue )

    if ($padSeqOneName <> $padSeqSeq and padExists (PAD . "sequence/types/$padSeqOneName/$padSeqFilterCheck.php")) {

      $GLOBALS["padSeq_".$padSeqOne. "_list"] [$padSeqOneName] = $padSeqOneValue;

      padDone ( $padSeqOneName, TRUE );

      $GLOBALS ["padSeq_$padSeqOneName"] = $padSeqOneValue;

    }
  
?>