<?php

  if ( ! isset( $padPrmsTag [$pad]['both']) and ! isset( $padPrmsTag [$pad]['left'])  and !  isset( $padPrmsTag [$pad]['right']) )
    $padPrmsTag [$pad]['both'] = TRUE;

  if ( isset( $padPrmsTag [$pad]['both']) ) {
    $padSeq_trucateCnt = pSeq_getCnt ( 'both', 'truncate', 'count');
    $padSeq_result        = pSeq_truncate  ( $padSeq_result, 'left',  $padSeq_trucateCnt );
    $padSeq_result        = pSeq_truncate  ( $padSeq_result, 'right', $padSeq_trucateCnt );
    pDone ( 'both', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['left']) ) {
    $padSeq_trucateCnt = pSeq_getCnt ( 'left', 'truncate', 'count');
    $padSeq_result        = pSeq_truncate  ( $padSeq_result, 'left', $padSeq_trucateCnt );
    pDone ( 'left', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['right']) ) {
    $padSeq_trucateCnt = pSeq_getCnt ( 'right', 'truncate', 'count');
    $padSeq_result        = pSeq_truncate  ( $padSeq_result, 'right', $padSeq_trucateCnt );
    pDone ( 'right', TRUE );
  }

  return $padSeq_result;
  
?>