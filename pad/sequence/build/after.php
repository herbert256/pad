<?php

  $pad_save_parms_tag = $pad_parms_tag;
  $pad_save_seq_seq   = $pad_seq_seq;
  $pad_save_seq_parm  = $pad_seq_parm;

  $pad_parms_tag_tmp = pad_explode ( $pad_parms_tag [$pad_seq_after], ';');

  $pad_parms_tag = [];
  foreach ( $pad_parms_tag_tmp as $pad_parms_tag_entry )
    $pad_parms_tag [$pad_parms_tag_entry ] = TRUE;

  $pad_seq_seq   = $pad_seq_after;
  $pad_seq_parm  = '*PAD_INTERNAL*';

  $pad_seq_store [$pad_seq_parm] = $pad_seq_result;
  $pad_seq_result = [];

  include PAD . "sequence/types/$pad_seq_after/$pad_seq_after.php";

  $pad_parms_tag = $pad_save_parms_tag;
  $pad_seq_seq   = $pad_save_seq_seq;
  $pad_seq_parm  = $pad_save_seq_parm;
  
?>