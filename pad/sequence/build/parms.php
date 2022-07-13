<?php

  $pad_seq_from     = intval ( $pad_parms_tag ['from']      ?? 1           );
  $pad_seq_to       = intval ( $pad_parms_tag ['to']        ?? PHP_INT_MAX );
  $pad_seq_min      = intval ( $pad_parms_tag ['min']       ?? 0           );
  $pad_seq_max      = intval ( $pad_parms_tag ['max']       ?? PHP_INT_MAX );
  $pad_seq_start    = intval ( $pad_parms_tag ['start']     ?? 0           );
  $pad_seq_end      = intval ( $pad_parms_tag ['end']       ?? PHP_INT_MAX );
  $pad_seq_low      = intval ( $pad_parms_tag ['low']       ?? 0           );
  $pad_seq_high     = intval ( $pad_parms_tag ['high']      ?? PHP_INT_MAX );
  $pad_seq_inc      = intval ( $pad_parms_tag ['increment'] ?? 1           );
  $pad_seq_rows     = intval ( $pad_parms_tag ['rows']      ?? 0           );
  $pad_seq_unique   = intval ( $pad_parms_tag ['unique']    ?? 0           );
  $pad_seq_random   = intval ( $pad_parms_tag ['random']    ?? 0           );
  $pad_seq_count    = intval ( $pad_parms_tag ['count']     ?? 0           );
  $pad_seq_page     = intval ( $pad_parms_tag ['page']      ?? 0           );
  $pad_seq_name     =          $pad_parms_tag ['name']      ?? ''; 
  $pad_seq_protect  =          $pad_parms_tag ['protect']   ?? 1000; 
  $pad_seq_save     =          $pad_parms_tag ['save']      ?? 100; 
  $pad_seq_unique   =          $pad_parms_tag ['unique']    ?? '';
  $pad_seq_push     =          $pad_parms_tag ['store']     ?? ''; 
  $pad_seq_pull     =          $pad_parms_tag ['sequence']  ?? '';
  $pad_seq_range    =          $pad_parms_tag ['range']     ?? '';
  $pad_seq_filter   =          $pad_parms_tag ['filter']    ?? '';
  $pad_seq_make     =          $pad_parms_tag ['make']      ?? '';
  $pad_seq_update   =          $pad_parms_tag ['update']    ?? '';

  unset ( $pad_parms_tag ['store'] );
  unset ( $pad_parameters [$pad_lvl] ['parms_tag'] ['store'] );

?>