<?php

  if ( ! isset( $padPrmsTag [$pad]['both']) and ! isset( $padPrmsTag [$pad]['left'])  and !  isset( $padPrmsTag [$pad]['right']) )
    $padPrmsTag [$pad]['both'] = TRUE;

  if ( isset( $padPrmsTag [$pad]['both']) ) {
    $padSeqTrucateCnt = padSeq_getCnt ( 'both', 'truncate', 'count');
    $padSeqResult        = padSeq_truncate  ( $padSeqResult, 'left',  $padSeqTrucateCnt );
    $padSeqResult        = padSeq_truncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'both', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['left']) ) {
    $padSeqTrucateCnt = padSeq_getCnt ( 'left', 'truncate', 'count');
    $padSeqResult        = padSeq_truncate  ( $padSeqResult, 'left', $padSeqTrucateCnt );
    padDone ( 'left', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['right']) ) {
    $padSeqTrucateCnt = padSeq_getCnt ( 'right', 'truncate', 'count');
    $padSeqResult        = padSeq_truncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'right', TRUE );
  }

  return $padSeqResult;
  
?>