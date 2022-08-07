<?php

  $pSeq_from     = intval ( $pPrmsTag[$p] ['from']      ?? 1           );
  $pSeq_to       = intval ( $pPrmsTag[$p] ['to']        ?? PHP_INT_MAX );
  $pSeq_min      = intval ( $pPrmsTag[$p] ['min']       ?? 0           );
  $pSeq_max      = intval ( $pPrmsTag[$p] ['max']       ?? PHP_INT_MAX );
  $pSeq_start    = intval ( $pPrmsTag[$p] ['start']     ?? 0           );
  $pSeq_end      = intval ( $pPrmsTag[$p] ['end']       ?? PHP_INT_MAX );
  $pSeq_low      = intval ( $pPrmsTag[$p] ['low']       ?? 0           );
  $pSeq_high     = intval ( $pPrmsTag[$p] ['high']      ?? PHP_INT_MAX );
  $pSeq_inc      = intval ( $pPrmsTag[$p] ['increment'] ?? 1           );
  $pSeq_rows     = intval ( $pPrmsTag[$p] ['rows']      ?? 0           );
  $pSeq_unique   = intval ( $pPrmsTag[$p] ['unique']    ?? 0           );
  $pSeq_random   = intval ( $pPrmsTag[$p] ['random']    ?? 0           );
  $pSeq_count    = intval ( $pPrmsTag[$p] ['count']     ?? 0           );
  $pSeq_page     = intval ( $pPrmsTag[$p] ['page']      ?? 0           );
  $pSeq_name     =          $pPrmsTag[$p] ['name']      ?? ''; 
  $pSeq_protect  =          $pPrmsTag[$p] ['protect']   ?? 1000; 
  $pSeq_save     =          $pPrmsTag[$p] ['save']      ?? 100; 
  $pSeq_unique   =          $pPrmsTag[$p] ['unique']    ?? '';
  $pSeq_push     =          $pPrmsTag[$p] ['store']     ?? ''; 
  $pSeq_pull     =          $pPrmsTag[$p] ['sequence']  ?? '';
  $pSeq_range    =          $pPrmsTag[$p] ['range']     ?? '';
  $pSeq_keep     =          $pPrmsTag[$p] ['keep']      ?? '';
  $pSeq_remove   =          $pPrmsTag[$p] ['remove']    ?? '';
  $pSeq_make     =          $pPrmsTag[$p] ['make']      ?? '';
  $pSeq_update   =          $pPrmsTag[$p] ['update']    ?? '';

  unset ( $pPrmsTag[$p] ['store'] );
  unset ( pParmsPrmsTag'] ['store'] );

  foreach ( $pPrmsTag[$p] as $pSeq_tag_name => $pSeq_tag_value )
    if ( ! isset($GLOBALS ["pSeq_$pSeq_tag_name"]) )
      $GLOBALS ["pSeq_$pSeq_tag_name"] = $pSeq_tag_value;

?>