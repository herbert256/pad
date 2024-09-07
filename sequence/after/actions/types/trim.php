<?php

  if ( isset ( $padPrm [$pad] ['both'] ) ) {
    $padSeqResult = padSeqTruncate  ( $padSeqResult, 'left',  $padPrm [$pad] ['both'] );
    $padSeqResult = padSeqTruncate  ( $padSeqResult, 'right', $padPrm [$pad] ['both'] );
  }

  if ( isset ( $padPrm [$pad] ['left'] ) )
    $padSeqResult = padSeqTruncate  ( $padSeqResult, 'left', $padPrm [$pad] ['left'] );

  if ( isset ( $padPrm [$pad] ['right'] ) ) 
    $padSeqResult = padSeqTruncate  ( $padSeqResult, 'right', $padPrm [$pad] ['right'] );

  return $padSeqResult;
  
?>