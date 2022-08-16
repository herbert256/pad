<?php

  if ( ! isset( $padPrmsTag [$pad]['both']) and ! isset( $padPrmsTag [$pad]['left'])  and !  isset( $padPrmsTag [$pad]['right']) )
    $padPrmsTag [$pad]['both'] = TRUE;

  if ( isset( $padPrmsTag [$pad]['both']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'both', 'truncate', 'count');
    $padSeqResult        = padSeqTruncate  ( $padSeqResult, 'left',  $padSeqTrucateCnt );
    $padSeqResult        = padSeqTruncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'both', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['left']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'left', 'truncate', 'count');
    $padSeqResult        = padSeqTruncate  ( $padSeqResult, 'left', $padSeqTrucateCnt );
    padDone ( 'left', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['right']) ) {
    $padSeqTrucateCnt = padSeqGetCnt ( 'right', 'truncate', 'count');
    $padSeqResult        = padSeqTruncate  ( $padSeqResult, 'right', $padSeqTrucateCnt );
    padDone ( 'right', TRUE );
  }

  return $padSeqResult;
  
?>