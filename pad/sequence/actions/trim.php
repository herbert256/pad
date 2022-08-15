<?php

  if ( ! isset( $padPrmsTag [$pad]['both']) and ! isset( $padPrmsTag [$pad]['left'])  and !  isset( $padPrmsTag [$pad]['right']) )
    $padPrmsTag [$pad]['both'] = TRUE;

  if ( isset( $padPrmsTag [$pad]['both']) ) {
    $padSeq_trucateCnt = padSeq_getCnt ( 'both', 'truncate', 'count');
    $padSeq_result        = padSeq_truncate  ( $padSeq_result, 'left',  $padSeq_trucateCnt );
    $padSeq_result        = padSeq_truncate  ( $padSeq_result, 'right', $padSeq_trucateCnt );
    padDone ( 'both', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['left']) ) {
    $padSeq_trucateCnt = padSeq_getCnt ( 'left', 'truncate', 'count');
    $padSeq_result        = padSeq_truncate  ( $padSeq_result, 'left', $padSeq_trucateCnt );
    padDone ( 'left', TRUE );
  }

  if ( isset( $padPrmsTag [$pad]['right']) ) {
    $padSeq_trucateCnt = padSeq_getCnt ( 'right', 'truncate', 'count');
    $padSeq_result        = padSeq_truncate  ( $padSeq_result, 'right', $padSeq_trucateCnt );
    padDone ( 'right', TRUE );
  }

  return $padSeq_result;
  
?>