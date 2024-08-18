<?php

  if ( ! isset( $padPrm [$pad]['both']) and ! isset( $padPrm [$pad]['left'])  and ! isset( $padPrm [$pad]['right']) )
    $padPrm [$pad]['both'] = TRUE;

  if ( isset( $padPrm [$pad]['both']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'both', 'truncate', 'count');
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'left',  $padSeqTrucateCnt );
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'both', TRUE );
  }

  if ( isset( $padPrm [$pad]['left']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'left', 'truncate', 'count');
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'left', $padSeqTrucateCnt );
    padDone ( 'left', TRUE );
  }

  if ( isset( $padPrm [$pad]['right']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'right', 'truncate', 'count');
    $padSeqResult     = padSeqTruncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'right', TRUE );
  }

  return $padSeqResult;
  
?>