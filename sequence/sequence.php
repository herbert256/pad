<?php

  $pad_seq_rows     = $pad_parms_tag ['rows']    ?? 0;
  $pad_seq_page     = $pad_parms_tag ['page']    ?? 0;
  $pad_seq_row      = $pad_parms_tag ['row']     ?? 0;
  $pad_seq_value    = $pad_parms_tag ['value']   ?? 0;
  $pad_seq_min      = $pad_parms_tag ['min']     ?? 0;
  $pad_seq_max      = $pad_parms_tag ['max']     ?? PHP_INT_MAX;
  $pad_seq_from     = $pad_parms_tag ['from']    ?? 0;
  $pad_seq_to       = $pad_parms_tag ['to']      ?? PHP_INT_MAX;
  $pad_seq_start    = $pad_parms_tag ['start']   ?? 0;
  $pad_seq_end      = $pad_parms_tag ['end']     ?? PHP_INT_MAX;
  $pad_seq_unique   = $pad_parms_tag ['unique']  ?? 0;
  $pad_seq_random   = $pad_parms_tag ['random']  ?? 0;
  $pad_seq_push     = $pad_parms_tag ['push']    ?? '';
  $pad_seq_pull     = $pad_parms_tag ['pull']    ?? ''; 
  $pad_seq_protect  = $pad_parms_tag ['protect'] ?? 10000;

  if ( isset($pad_parms_tag ['walk']) )
    return include 'walk.php';
  else 
    return include 'build.php';

?>