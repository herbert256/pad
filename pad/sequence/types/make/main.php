<?php

  $GLOBALS ["pad_seq_mf_$pad_seq_seq"] = [];

  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value )

    if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_opt_name/$pad_seq_seq.php" ) ) {

      $GLOBALS ["pad_seq_mf_$pad_seq_seq"] [$pad_seq_opt_name] = $pad_seq_opt_value;

      pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_opt_name"] = $pad_seq_opt_value;

    }
   
  $pad_seq_store_get = $pad_seq_parm;
  $pad_seq_for = $pad_seq_store [$pad_seq_parm];

  if ( ! $pad_seq_push and ! $pad_pair )
    $pad_seq_push = $pad_seq_parm;

  include PAD . "sequence/type/for.php";
      
?>