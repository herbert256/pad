<?php

  if ( ! isset( $pPrmsTag [$p]['both']) and ! isset( $pPrmsTag [$p]['left'])  and !  isset( $pPrmsTag [$p]['right']) )
    $pPrmsTag [$p]['both'] = TRUE;

  if ( isset( $pPrmsTag [$p]['both']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'both', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left',  $pSeq_trucate_count );
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucate_count );
    pDone ( 'both', TRUE );
  }

  if ( isset( $pPrmsTag [$p]['left']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'left', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left', $pSeq_trucate_count );
    pDone ( 'left', TRUE );
  }

  if ( isset( $pPrmsTag [$p]['right']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'right', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucate_count );
    pDone ( 'right', TRUE );
  }

  return $pSeq_result;
  
?>