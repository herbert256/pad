<?php

  $padSeq_from     = intval ( $padPrmsTag [$pad] ['from']      ?? 1           );
  $padSeq_to       = intval ( $padPrmsTag [$pad] ['to']        ?? PHP_INT_MAX );
  $padSeq_min      = intval ( $padPrmsTag [$pad] ['min']       ?? 0           );
  $padSeq_max      = intval ( $padPrmsTag [$pad] ['max']       ?? PHP_INT_MAX );
  $padSeq_start    = intval ( $padPrmsTag [$pad] ['start']     ?? 0           );
  $padSeq_end      = intval ( $padPrmsTag [$pad] ['end']       ?? PHP_INT_MAX );
  $padSeq_low      = intval ( $padPrmsTag [$pad] ['low']       ?? 0           );
  $padSeq_high     = intval ( $padPrmsTag [$pad] ['high']      ?? PHP_INT_MAX );
  $padSeq_inc      = intval ( $padPrmsTag [$pad] ['increment'] ?? 1           );
  $padSeq_rows     = intval ( $padPrmsTag [$pad] ['rows']      ?? 0           );
  $padSeq_unique   = intval ( $padPrmsTag [$pad] ['unique']    ?? 0           );
  $padSeq_random   = intval ( $padPrmsTag [$pad] ['random']    ?? 0           );
  $padSeqCnt       = intval ( $padPrmsTag [$pad] ['count']     ?? 0           );
  $padSeq_page     = intval ( $padPrmsTag [$pad] ['page']      ?? 0           );
  $padSeq_name     =          $padName [$pad]      ?? ''; 
  $padSeq_protect  =          $padPrmsTag [$pad] ['protect']   ?? 1000; 
  $padSeq_save     =          $padPrmsTag [$pad] ['save']      ?? 100; 
  $padSeq_unique   =          $padPrmsTag [$pad] ['unique']    ?? '';
  $padSeq_push     =          $padPrmsTag [$pad] ['store']     ?? ''; 
  $padSeq_pull     =          $padPrmsTag [$pad] ['sequence']  ?? '';
  $padSeq_range    =          $padPrmsTag [$pad] ['range']     ?? '';
  $padSeq_keep     =          $padPrmsTag [$pad] ['keep']      ?? '';
  $padSeq_remove   =          $padPrmsTag [$pad] ['remove']    ?? '';
  $padSeq_make     =          $padPrmsTag [$pad] ['make']      ?? '';
  $padSeq_update   =          $padPrmsTag [$pad] ['update']    ?? '';

  unset ( $padPrmsTag [$pad] ['store'] );
 
  foreach ( $padPrmsTag [$pad] as $padSeq_tag_name => $padSeq_tag_value )
    if ( ! isset($GLOBALS ["padSeq_$padSeq_tag_name"]) )
      $GLOBALS ["padSeq_$padSeq_tag_name"] = $padSeq_tag_value;

?>