<?php

  pad_set_arr_var ( 'done', $pad_seq_seq,  TRUE );
  pad_set_arr_var ( 'done', $pad_seq_name, TRUE );
  pad_set_arr_var ( 'done', 'from',        TRUE );
  pad_set_arr_var ( 'done', 'increment',   TRUE );
  pad_set_arr_var ( 'done', 'to',          TRUE );
  pad_set_arr_var ( 'done', 'rows',        TRUE );
  pad_set_arr_var ( 'done', 'min',         TRUE );
  pad_set_arr_var ( 'done', 'max',         TRUE );
  pad_set_arr_var ( 'done', 'unique',      TRUE );
  pad_set_arr_var ( 'done', 'page',        TRUE );
  pad_set_arr_var ( 'done', 'start',       TRUE );
  pad_set_arr_var ( 'done', 'end',         TRUE );
  pad_set_arr_var ( 'done', 'low',         TRUE );
  pad_set_arr_var ( 'done', 'high',        TRUE );
  pad_set_arr_var ( 'done', 'random',      TRUE );
  pad_set_arr_var ( 'done', 'push',        TRUE );
  pad_set_arr_var ( 'done', 'store',       TRUE );
  pad_set_arr_var ( 'done', 'pull',        TRUE );
  pad_set_arr_var ( 'done', 'sequence',    TRUE );
  pad_set_arr_var ( 'done', 'range',       TRUE );
  pad_set_arr_var ( 'done', 'protect',     TRUE );
  pad_set_arr_var ( 'done', 'keep',        TRUE );
  pad_set_arr_var ( 'done', 'remove',      TRUE );
  pad_set_arr_var ( 'done', 'make',        TRUE );

  foreach ( $pad_prms_tag as $pad_seq_tag_name => $pad_seq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/make.php" ) )
      pad_set_arr_var ( 'done', $pad_seq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/filter.php" ) )
      pad_set_arr_var ( 'done', $pad_seq_tag_name, TRUE );

  }

?>