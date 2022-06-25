<?php
  

  if ( $pad_seq_one == $pad_seq_seq ) 
    return;
  
  $GLOBALS["pad_seq_".$pad_seq_one. "_list"] = [];

  $pad_seq_one_tmp = pad_explode ( $pad_parms_tag [$pad_seq_one], ';');

  $pad_seq_one = [];
  foreach ( $pad_seq_one_tmp as $pad_parms_tag_entry )
    $pad_seq_one [$pad_parms_tag_entry ] = TRUE;

  foreach ( $pad_seq_one as $pad_seq_one_name => $pad_seq_one_value )

    if ($pad_seq_one_name <> $pad_seq_one and pad_file_exists (PAD . "sequence/types/$pad_seq_one_name/$pad_seq_one.php")) {

      $GLOBALS["pad_seq_".$pad_seq_one. "_list"] = $pad_seq_one_name;

      pad_set_arr_var ( 'options_done', $pad_seq_one_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_one_name"] = $pad_seq_one_value;

    }
  
?>