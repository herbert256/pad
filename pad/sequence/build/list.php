<?php

  $GLOBALS ["padSeq_$padSeqOne" . "_list"] = [];

  if ( $padSeqOne == $padSeqSeq ) 
    return;

  if ( ! isset($padPrmsTag [$pad] [$padSeqOne]) )
    return;

  $GLOBALS["padSeq_".$padSeqOne. "_list"] = [];

  $padSeqOneTmp = padExplode ( $padPrmsTag [$pad] [$padSeqOne], ';');

  $padSeqOneList = [];
  foreach ( $padSeqOneTmp as $padEntry )
    $padSeqOneList [$padEntry ] = TRUE;

  foreach ( $padSeqOneList as $padSeqOneName => $padSeqOneValue )

    if ($padSeqOneName <> $padSeqSeq and file_exists (PAD . "pad/sequence/types/$padSeqOneName/$padSeqFilterCheck.php")) {

      $GLOBALS["padSeq_".$padSeqOne. "_list"] [$padSeqOneName] = $padSeqOneValue;

      padDone ( $padSeqOneName, TRUE );

      $GLOBALS ["padSeq_$padSeqOneName"] = $padSeqOneValue;

    }
  
?>