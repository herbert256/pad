<?php

  if ( ! isset( $pPrms_tag['both']) and ! isset( $pPrms_tag['left'])  and !  isset( $pPrms_tag['right']) )
    $pPrms_tag['both'] = TRUE;

  if ( isset( $pPrms_tag['both']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'both', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left',  $pSeq_trucate_count );
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucate_count );
    pDone ( 'both', TRUE );
  }

  if ( isset( $pPrms_tag['left']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'left', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'left', $pSeq_trucate_count );
    pDone ( 'left', TRUE );
  }

  if ( isset( $pPrms_tag['right']) ) {
    $pSeq_trucate_count = pSeq_get_count ( 'right', 'truncate', 'count');
    $pSeq_result        = pSeq_truncate  ( $pSeq_result, 'right', $pSeq_trucate_count );
    pDone ( 'right', TRUE );
  }

  return $pSeq_result;
  
?>