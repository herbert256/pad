<?php

  $pSeq_from     = intval ( $pPrms_tag ['from']      ?? 1           );
  $pSeq_to       = intval ( $pPrms_tag ['to']        ?? PHP_INT_MAX );
  $pSeq_min      = intval ( $pPrms_tag ['min']       ?? 0           );
  $pSeq_max      = intval ( $pPrms_tag ['max']       ?? PHP_INT_MAX );
  $pSeq_start    = intval ( $pPrms_tag ['start']     ?? 0           );
  $pSeq_end      = intval ( $pPrms_tag ['end']       ?? PHP_INT_MAX );
  $pSeq_low      = intval ( $pPrms_tag ['low']       ?? 0           );
  $pSeq_high     = intval ( $pPrms_tag ['high']      ?? PHP_INT_MAX );
  $pSeq_inc      = intval ( $pPrms_tag ['increment'] ?? 1           );
  $pSeq_rows     = intval ( $pPrms_tag ['rows']      ?? 0           );
  $pSeq_unique   = intval ( $pPrms_tag ['unique']    ?? 0           );
  $pSeq_random   = intval ( $pPrms_tag ['random']    ?? 0           );
  $pSeq_count    = intval ( $pPrms_tag ['count']     ?? 0           );
  $pSeq_page     = intval ( $pPrms_tag ['page']      ?? 0           );
  $pSeq_name     =          $pPrms_tag ['name']      ?? ''; 
  $pSeq_protect  =          $pPrms_tag ['protect']   ?? 1000; 
  $pSeq_save     =          $pPrms_tag ['save']      ?? 100; 
  $pSeq_unique   =          $pPrms_tag ['unique']    ?? '';
  $pSeq_push     =          $pPrms_tag ['store']     ?? ''; 
  $pSeq_pull     =          $pPrms_tag ['sequence']  ?? '';
  $pSeq_range    =          $pPrms_tag ['range']     ?? '';
  $pSeq_keep     =          $pPrms_tag ['keep']      ?? '';
  $pSeq_remove   =          $pPrms_tag ['remove']    ?? '';
  $pSeq_make     =          $pPrms_tag ['make']      ?? '';
  $pSeq_update   =          $pPrms_tag ['update']    ?? '';

  unset ( $pPrms_tag ['store'] );
  unset ( $pParms [$p] ['parms_tag'] ['store'] );

  foreach ( $pPrms_tag as $pSeq_tag_name => $pSeq_tag_value )
    if ( ! isset($GLOBALS ["pSeq_$pSeq_tag_name"]) )
      $GLOBALS ["pSeq_$pSeq_tag_name"] = $pSeq_tag_value;

?>