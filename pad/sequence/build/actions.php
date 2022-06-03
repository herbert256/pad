<?php
  
  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value )

    if ( $pad_seq_opt_name <> $pad_seq_seq )

      if ( pad_file_exists ( PAD . "sequence/actions/one/$pad_seq_opt_name.php" ) ) {

        foreach ( $pad_seq_result as $pad_k => $pad_seq_action)
          $pad_seq_result [$pad_k] = include PAD . "sequence/actions/one/$pad_seq_opt_name.php";

        pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );

      } elseif ( pad_file_exists ( PAD . "sequence/actions/list/$pad_seq_opt_name.php" ) ) {

        include PAD . "sequence/actions/list/$pad_seq_opt_name.php";

        pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );

      }
  
?>