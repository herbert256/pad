<?php

  pad_set_arr_var ( 'options_done', $pad_seq_seq,  TRUE );
  pad_set_arr_var ( 'options_done', $pad_seq_name, TRUE );
  pad_set_arr_var ( 'options_done', 'from',        TRUE );
  pad_set_arr_var ( 'options_done', 'increment',   TRUE );
  pad_set_arr_var ( 'options_done', 'to',          TRUE );
  pad_set_arr_var ( 'options_done', 'rows',        TRUE );
  pad_set_arr_var ( 'options_done', 'min',         TRUE );
  pad_set_arr_var ( 'options_done', 'max',         TRUE );
  pad_set_arr_var ( 'options_done', 'unique',      TRUE );
  pad_set_arr_var ( 'options_done', 'page',        TRUE );
  pad_set_arr_var ( 'options_done', 'start',       TRUE );
  pad_set_arr_var ( 'options_done', 'end',         TRUE );
  pad_set_arr_var ( 'options_done', 'low',         TRUE );
  pad_set_arr_var ( 'options_done', 'high',        TRUE );
  pad_set_arr_var ( 'options_done', 'random',      TRUE );
  pad_set_arr_var ( 'options_done', 'push',        TRUE );
  pad_set_arr_var ( 'options_done', 'store',       TRUE );
  pad_set_arr_var ( 'options_done', 'pull',        TRUE );
  pad_set_arr_var ( 'options_done', 'sequence',    TRUE );
  pad_set_arr_var ( 'options_done', 'range',       TRUE );
  pad_set_arr_var ( 'options_done', 'protect',     TRUE );
  pad_set_arr_var ( 'options_done', 'keep',        TRUE );
  pad_set_arr_var ( 'options_done', 'remove',      TRUE );
  pad_set_arr_var ( 'options_done', 'make',        TRUE );

  foreach ( $pad_prms_tag as $pad_seq_tag_name => $pad_seq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/make.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/filter.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );

  }

?>