<?php

  if ( ! $padSeqPush and ! $padPair[$pad] )
    $padSeqPush = TRUE;

  if ( ! $padSeqPush )
    return;

  if ( $padSeqPush === TRUE ) 
    if     ( isset ( $padPrm [$pad] ['name'] )    ) $padSeqPush = $padPrm [$pad] ['name']; 
    elseif ( $padSeqPull and $padSeqPull !== TRUE ) $padSeqPush = $padSeqPull;
    else                                            $padSeqPush = $padSeqName;

  $padSeqStore [$padSeqPush] = $padSeqResult;

?>