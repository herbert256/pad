<?php

  $pad_seq_from     = intval ( $pad_prms_tag ['from']      ?? 1           );
  $pad_seq_to       = intval ( $pad_prms_tag ['to']        ?? PHP_INT_MAX );
  $pad_seq_min      = intval ( $pad_prms_tag ['min']       ?? 0           );
  $pad_seq_max      = intval ( $pad_prms_tag ['max']       ?? PHP_INT_MAX );
  $pad_seq_start    = intval ( $pad_prms_tag ['start']     ?? 0           );
  $pad_seq_end      = intval ( $pad_prms_tag ['end']       ?? PHP_INT_MAX );
  $pad_seq_low      = intval ( $pad_prms_tag ['low']       ?? 0           );
  $pad_seq_high     = intval ( $pad_prms_tag ['high']      ?? PHP_INT_MAX );
  $pad_seq_inc      = intval ( $pad_prms_tag ['increment'] ?? 1           );
  $pad_seq_rows     = intval ( $pad_prms_tag ['rows']      ?? 0           );
  $pad_seq_unique   = intval ( $pad_prms_tag ['unique']    ?? 0           );
  $pad_seq_random   = intval ( $pad_prms_tag ['random']    ?? 0           );
  $pad_seq_count    = intval ( $pad_prms_tag ['count']     ?? 0           );
  $pad_seq_page     = intval ( $pad_prms_tag ['page']      ?? 0           );
  $pad_seq_name     =          $pad_prms_tag ['name']      ?? ''; 
  $pad_seq_protect  =          $pad_prms_tag ['protect']   ?? 1000; 
  $pad_seq_save     =          $pad_prms_tag ['save']      ?? 100; 
  $pad_seq_unique   =          $pad_prms_tag ['unique']    ?? '';
  $pad_seq_push     =          $pad_prms_tag ['store']     ?? ''; 
  $pad_seq_pull     =          $pad_prms_tag ['sequence']  ?? '';
  $pad_seq_range    =          $pad_prms_tag ['range']     ?? '';
  $pad_seq_keep     =          $pad_prms_tag ['keep']      ?? '';
  $pad_seq_remove   =          $pad_prms_tag ['remove']    ?? '';
  $pad_seq_make     =          $pad_prms_tag ['make']      ?? '';
  $pad_seq_update   =          $pad_prms_tag ['update']    ?? '';

  unset ( $pad_prms_tag ['store'] );
  unset ( $pad_parms [$pad] ['parms_tag'] ['store'] );

  foreach ( $pad_prms_tag as $pad_seq_tag_name => $pad_seq_tag_value )
    if ( ! isset($GLOBALS ["pad_seq_$pad_seq_tag_name"]) )
      $GLOBALS ["pad_seq_$pad_seq_tag_name"] = $pad_seq_tag_value;

?>