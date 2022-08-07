<?php

  if ( ! isset( $pPrmsTag[$p]['both']) and ! isset( $pPrmsTag[$p]['left'])  and !  isset( $pPrmsTag[$p]['right']) )
    $pPrmsTag[$p]['both'] = TRUE;

  if ( isset( $pPrmsTag[$p]['both']) ) {
    $pSeq_trucateCnt = pSeq_getCnt ( 'both', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left',  $pSeq_trucateCnt );
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucateCnt );
    pDone ( 'both', TRUE );
  }

  if ( isset( $pPrmsTag[$p]['left']) ) {
    $pSeq_trucateCnt = pSeq_getCnt ( 'left', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left', $pSeq_trucateCnt );
    pDone ( 'left', TRUE );
  }

  if ( isset( $pPrmsTag[$p]['right']) ) {
    $pSeq_trucateCnt = pSeq_getCnt ( 'right', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucateCnt );
    pDone ( 'right', TRUE );
  }

  return $pSeq_result;
  
?>