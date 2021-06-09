<?php

  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value) {

    if ( $pad_seq_opt_name == $pad_tag )
      continue;

    if ( pad_file_exists ( PAD_HOME . "sequence/options/$pad_seq_opt_name.php" ) ) {

      pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );

      include PAD_HOME . "sequence/options/$pad_seq_opt_name.php";

      continue;
      
    }

    if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_seq_opt_name/check.php" ) ) {

      pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );

      foreach ( $pad_seq_result as $pad_seq_key => $pad_seq_check )
        if ( ! include PAD_HOME . "sequence/types/$pad_seq_opt_name/check.php" )     
          unset ( $pad_seq_result [$pad_seq_key] );

    }

  }

?>