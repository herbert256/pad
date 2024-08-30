<?php

  if ( isset( $padPrm [$pad]['both']) ) {
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'left',  $padSeqBoth );
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'right', $padSeqBoth );
  }

  if ( isset( $padPrm [$pad]['left']) )
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'left', $padSeqLeft );

  if ( isset( $padPrm [$pad]['right']) ) 
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'right', $padSeqRight );

  return $padSeqResult;
  
?>