<?php

  $pad_seq_filter = [];

  foreach ( $pad_parms_tag as $pad_seq_filter_name => $pad_seq_filter_value )

    if ( $pad_seq_filter_name <> 'filter' and pad_file_exists ( PAD . "sequence/types/$pad_seq_filter_name/filter.php" ) ) {

      $pad_seq_filter [] = $pad_seq_filter_name;

      pad_set_arr_var ( 'options_done', $pad_seq_filter_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_filter_name"] = $pad_seq_filter_value;

    }
   
  $pad_seq_for = $pad_seq_store [$pad_seq_parm];

  include PAD . "sequence/type/for.php";
       
?>