<?php

  $GLOBALS ["pad_seq_$pad_seq_one" . "_list"] = [];

  if ( $pad_seq_one == $pad_seq_seq ) 
    return;

  if ( ! isset($pad_prms_tag [$pad_seq_one]) )
    return;

  $GLOBALS["pad_seq_".$pad_seq_one. "_list"] = [];

  $pad_seq_one_tmp = pad_explode ( $pad_prms_tag [$pad_seq_one], ';');

  $pad_seq_one_list = [];
  foreach ( $pad_seq_one_tmp as $pad_prms_tag_entry )
    $pad_seq_one_list [$pad_prms_tag_entry ] = TRUE;

  foreach ( $pad_seq_one_list as $pad_seq_one_name => $pad_seq_one_value )

    if ($pad_seq_one_name <> $pad_seq_seq and file_exists (PAD . "sequence/types/$pad_seq_one_name/$pad_seq_filter_check.php")) {

      $GLOBALS["pad_seq_".$pad_seq_one. "_list"] [$pad_seq_one_name] = $pad_seq_one_value;

      pad_set_arr_var ( 'done', $pad_seq_one_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_one_name"] = $pad_seq_one_value;

    }
  
?>