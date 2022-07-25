<?php

  $pad_sequence_store_get = '';
  $pad_seq_result = $pad_seq_for = $pad_seq_cache = [];
  $pad_seq = $pad_sequence = $pad_seq_protect_cnt = $pad_seq_base = 0;

  if ( ! $pad_seq_name )                            $pad_seq_name = $pad_seq_set; 
  if ( ! isset($GLOBALS ["pad_seq_$pad_seq_seq"]) ) $GLOBALS ["pad_seq_$pad_seq_seq"] = $pad_seq_parm;
  if ( ! isset($pad_parms_tag ["$pad_seq_seq"])   ) $pad_parms_tag ["$pad_seq_seq"]   = $pad_seq_parm;

  if ( $pad_seq_seq == 'make' )
    $pad_seq_filter_check = 'make';
  else
    $pad_seq_filter_check = 'filter';

  $pad_seq_special_ops = ['make', 'keep', 'remove'];

  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

?>